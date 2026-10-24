// Teste manual: abre a tela de login em tempo real e salva capturas em instantes da abertura animada.
// Uso: node capture-login.mjs <pasta-de-saída> [largura] [altura]
import puppeteer from 'puppeteer-core';
import { existsSync, mkdirSync } from 'node:fs';
import path from 'node:path';

const out = process.argv[2];
const W = Number(process.argv[3] || 1440), H = Number(process.argv[4] || 900);
if (!out) throw new Error('informe a pasta de saída');
mkdirSync(out, { recursive: true });

const EDGE = [
  'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe',
  'C:/Program Files/Microsoft/Edge/Application/msedge.exe',
].find(existsSync);

const browser = await puppeteer.launch({ executablePath: EDGE, headless: 'new' });
const page = await browser.newPage();
await page.setViewport({ width: W, height: H });
const errors = [];
page.on('pageerror', e => errors.push(e.message));

await page.goto('http://127.0.0.1:8091/diomar-cs/frontend/pages/login.php?splash=1', { waitUntil: 'domcontentloaded' });
const t0 = Date.now();
for (const ms of [500, 1000, 1500, 1900, 2250, 2550, 2800, 3300, 4500]) {
  const wait = ms - (Date.now() - t0);
  if (wait > 0) await new Promise(r => setTimeout(r, wait));
  await page.screenshot({ path: path.join(out, `t${ms}.png`) });
}
console.log('erros de JS:', errors.length ? errors : 'nenhum');
console.log('overlay ainda existe:', await page.evaluate(() => !!document.getElementById('splash')));
await browser.close();
