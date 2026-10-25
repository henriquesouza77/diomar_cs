// Testes de segurança da API (sem dependências). Precisa do site rodando com banco.
//
//   node tests/api-security.test.mjs [URL_BASE]      (padrão: http://localhost/diomar-cs)
//
// Cuidado: o teste de login faz 6 tentativas erradas, que contam no limite por IP
// (20 a cada 15 min). Não rode mais de umas 3 vezes seguidas.
import assert from 'node:assert/strict';

const BASE = (process.argv[2] || 'http://localhost/diomar-cs').replace(/\/$/, '');
const API = `${BASE}/backend/api/index.php`;

// Visitante anônimo: pega sessão e token CSRF da página de login, como um navegador
async function visitor() {
  const page = await fetch(`${BASE}/frontend/pages/login.php?splash=0`);
  const cookie = page.headers.getSetCookie().map(c => c.split(';')[0]).join('; ');
  const html = await page.text();
  const token = html.match(/name="csrf-token" content="([^"]+)"/)?.[1];
  assert.ok(token, 'não achei o token CSRF na página de login');

  return async function call(route, { method = 'POST', body } = {}) {
    const res = await fetch(`${API}?route=${route}`, {
      method,
      headers: { Cookie: cookie, 'X-CSRF-Token': token, 'Content-Type': 'application/json' },
      body: body ? JSON.stringify(body) : undefined,
    });
    return { status: res.status, json: await res.json().catch(() => null) };
  };
}

const tests = [
  ['visitante NÃO remove foto de imóvel', async (call) => {
    assert.equal((await call('property-images/remove', { body: { id: 1 } })).status, 401);
  }],
  ['visitante NÃO reordena fotos', async (call) => {
    assert.equal((await call('property-images/reorder', { body: { ids: [3, 2, 1] } })).status, 401);
  }],
  ['visitante NÃO define foto principal', async (call) => {
    assert.equal((await call('property-images/set-primary', { body: { property_id: 1, image_id: 1 } })).status, 401);
  }],
  ['visitante NÃO muda status de lead', async (call) => {
    assert.equal((await call('leads/update-status', { body: { id: 1, status_id: 2 } })).status, 401);
  }],
  ['visitante NÃO salva configurações', async (call) => {
    assert.equal((await call('settings/save', { body: { site_name: 'x' } })).status, 401);
  }],
  ['6ª senha errada seguida é bloqueada (429)', async (call) => {
    const email = `teste+${Date.now()}@example.com`;
    const statuses = [];
    for (let i = 0; i < 6; i++) {
      statuses.push((await call('auth/login', { body: { email, password: `errada-${i}` } })).status);
    }
    assert.deepEqual(statuses, [401, 401, 401, 401, 401, 429]);
  }],
  ['campo-isca preenchido é descartado sem erro', async (call) => {
    const r = await call('leads/store', { body: { name: 'Robô', email: 'robo@example.com', website: 'http://spam' } });
    assert.equal(r.status, 200);
    assert.equal(r.json?.data?.id, 0);
  }],
];

let failed = 0;
for (const [name, fn] of tests) {
  try {
    await fn(await visitor());
    console.log(`  ok    ${name}`);
  } catch (e) {
    failed++;
    console.log(`  FALHA ${name}\n        ${e.message.split('\n')[0]}`);
  }
}
console.log(failed ? `\n${failed} teste(s) falharam.` : '\nTodos os testes passaram.');
process.exit(failed ? 1 : 0);
