// Polyfill Web Crypto API untuk Node.js 18.x
// (tersedia native di Node.js 19+, di 18.x perlu ini)
import { webcrypto } from 'node:crypto';
if (!globalThis.crypto) {
    globalThis.crypto = webcrypto;
}

import {
    default as makeWASocket,
    useMultiFileAuthState,
    delay,
    DisconnectReason,
    fetchLatestBaileysVersion
} from '@whiskeysockets/baileys';
import pino from 'pino';
import qrcode from 'qrcode-terminal';
import express from 'express';
import { existsSync, readFileSync, rmSync } from 'fs';
import { extname } from 'path';

const app = express();
app.use(express.json());

let sock;
let isConnected = false;
let contacts = new Set();

async function connectToWhatsApp() {
    console.log('Fetching latest WhatsApp version...');
    try {
        const { version, isLatest } = await fetchLatestBaileysVersion();
        console.log(`Using WA v${version.join('.')}, isLatest: ${isLatest}`);

        const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');

        sock = makeWASocket({
            version,
            auth: state,
            logger: pino({ level: 'silent' }),
            browser: ['Ubuntu', 'Chrome', '20.0.04']
        });

        sock.ev.on('creds.update', saveCreds);

        // Track contacts to populate statusJidList
        sock.ev.on('contacts.upsert', (newContacts) => {
            newContacts.forEach(c => {
                if (c.id && c.id.endsWith('@s.whatsapp.net')) {
                    contacts.add(c.id);
                }
            });
        });

        sock.ev.on('contacts.update', (updates) => {
            updates.forEach(c => {
                if (c.id && c.id.endsWith('@s.whatsapp.net')) {
                    contacts.add(c.id);
                }
            });
        });

        sock.ev.on('messaging-history.set', ({ contacts: newContacts }) => {
            if (newContacts) {
                newContacts.forEach(c => {
                    if (c.id && c.id.endsWith('@s.whatsapp.net')) {
                        contacts.add(c.id);
                    }
                });
            }
        });

        sock.ev.on('connection.update', (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                console.log('\n--- SCAN QR CODE INI DENGAN WHATSAPP ---');
                qrcode.generate(qr, { small: true });
                console.log('----------------------------------------\n');
            }

            if (connection === 'close') {
                const statusCode = (lastDisconnect?.error)?.output?.statusCode;
                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;
                console.log(`Connection closed (Reason: ${statusCode}). Reconnecting: ${shouldReconnect}`);
                isConnected = false;

                if (statusCode === DisconnectReason.loggedOut) {
                    console.log('Sesi login kedaluwarsa. Menghapus folder sesi...');
                    rmSync('auth_info_baileys', { recursive: true, force: true });
                    console.log('Silakan jalankan ulang script untuk SCAN QR BARU.');
                }

                if (shouldReconnect) {
                    setTimeout(connectToWhatsApp, 3000);
                }
            } else if (connection === 'open') {
                console.log('WHATSAPP SUDAH TERHUBUNG!');
                isConnected = true;
            }
        });
    } catch (err) {
        console.error('Failed to start:', err);
        setTimeout(connectToWhatsApp, 5000);
    }
}

app.post('/send-status', async (req, res) => {
    const { filePath, caption } = req.body;

    if (!isConnected) {
        return res.status(503).json({ success: false, message: 'WhatsApp is not connected.' });
    }

    try {
        if (!existsSync(filePath)) {
            return res.status(404).json({ success: false, message: 'File not found: ' + filePath });
        }

        const buffer = readFileSync(filePath);
        const ext = extname(filePath).toLowerCase();
        const isVideo = ext === '.mp4' || ext === '.mov';

        const randomDelay = Math.floor(Math.random() * (15000 - 5000 + 1)) + 5000;
        console.log(`Delay ${randomDelay / 1000}s sebelum kirim...`);
        await delay(randomDelay);

        let jidList = Array.from(contacts);
        console.log(`Mengirim ke ${jidList.length} kontak...`);

        await sock.sendMessage('status@broadcast', {
            [isVideo ? 'video' : 'image']: buffer,
            caption: caption || ''
        }, {
            statusJidList: jidList,
            broadcast: true
        });

        console.log('Status terkirim!');
        res.json({ success: true, message: 'Status sent!' });
    } catch (err) {
        console.error('Error kirim status:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

app.get('/', (req, res) => {
    res.send(`
        <html>
            <body style="font-family: sans-serif; text-align: center; padding-top: 50px;">
                <h1 style="color: #25D366;">WhatsApp Bridge is Running</h1>
                <p>Status: <strong>${isConnected ? '✅ Connected' : '⏳ Connecting...'}</strong></p>
                <p>Kontak ter-track: <strong>${contacts.size}</strong></p>
                <p>Kirim status via POST ke <code>/send-status</code></p>
            </body>
        </html>
    `);
});

const PORT = 3000;
app.listen(PORT, '127.0.0.1', () => {
    console.log(`Bridge Node Port: ${PORT}`);
    connectToWhatsApp();
});
