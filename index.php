<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mail Your Name — POST</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>✈️</text></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Caveat:wght@600&family=IBM+Plex+Mono:wght@400;600&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">

<style>
:root{
  --bg:#131f5e; --bg-deep:#0b1340;
  --paper:#f6f3ea; --paper-2:#fdfbf4;
  --ink:#17224f; --ink-soft:rgba(23,34,79,.62);
  --red:#e8402f; --red-dark:#b52a1c; --blue:#2742d6;
  --mono:'IBM Plex Mono',monospace; --disp:'Archivo Black',sans-serif;
}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{
  font-family:'Space Grotesk',sans-serif; color:#fff; min-height:100vh;
  background:radial-gradient(120% 90% at 20% 0%, #1a2a78 0%, var(--bg) 45%, var(--bg-deep) 100%);
  overflow-x:hidden;
}
::selection{background:var(--red);color:#fff}
:focus-visible{outline:3px dashed var(--red);outline-offset:3px}

/* ── ticker ─────────────────────────────── */
.ticker{background:var(--red);border-bottom:3px solid var(--red-dark);overflow:hidden}
.ticker-track{display:flex;width:max-content;animation:ticker 26s linear infinite}
.ticker-track > span{
  display:inline-block;white-space:nowrap;padding:.5rem 0;
  font-family:var(--mono);font-size:.72rem;letter-spacing:.22em;text-transform:uppercase;
}
@keyframes ticker{to{transform:translateX(-50%)}}

/* ── layout ─────────────────────────────── */
.stage{
  display:grid;grid-template-columns:minmax(0,1.05fr) minmax(0,.95fr);
  gap:clamp(2rem,5vw,4.5rem);align-items:center;
  max-width:1180px;margin:0 auto;padding:clamp(2rem,5vh,4rem) clamp(1.2rem,4vw,3rem) 3rem;
  min-height:calc(100vh - 40px);
}

/* ── left: copy ─────────────────────────── */
.kicker{font-family:var(--mono);font-size:.78rem;letter-spacing:.28em;text-transform:uppercase;color:#ffd24a;margin-bottom:1.2rem}
.headline{font-family:var(--disp);font-weight:400;text-transform:uppercase;line-height:.98;margin-bottom:1.4rem}
.h-line{display:block;font-size:clamp(2.6rem,6vw,4.6rem);letter-spacing:.01em}
.h-outline{color:transparent;-webkit-text-stroke:2px #fff;transition:color .35s}
.h-outline:hover{color:#fff}
.post-chip{
  display:inline-block;font-style:normal;background:var(--red);color:#fff;
  padding:.02em .28em .08em;transform:rotate(-3deg);
  box-shadow:4px 4px 0 rgba(0,0,0,.28);transition:transform .25s;
}
.post-chip:hover{transform:rotate(2deg) scale(1.05)}
.lede{max-width:46ch;font-size:1.02rem;line-height:1.65;color:rgba(255,255,255,.78);margin-bottom:2rem}
.lede code{font-family:var(--mono);font-size:.85em;background:rgba(255,255,255,.1);padding:.1em .4em;border-radius:4px}

.specs{max-width:440px;margin-bottom:2.2rem}
.spec{display:flex;justify-content:space-between;gap:1rem;align-items:baseline;
  padding:.55rem .2rem;border-bottom:1px dashed rgba(255,255,255,.28);
  transition:transform .25s,border-color .25s}
.spec:hover{transform:translateX(6px);border-color:#ffd24a}
.spec dt{font-family:var(--mono);font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(255,255,255,.55)}
.spec dd{font-family:var(--mono);font-size:.9rem;font-weight:600}

/* flight-path decoration */
.flight{position:relative;height:130px;max-width:560px}
.flight svg{position:absolute;inset:0;width:100%;height:100%;overflow:visible}
.flight .route{stroke:rgba(255,255,255,.35);stroke-width:2;fill:none;stroke-dasharray:7 9;animation:dash 1.4s linear infinite}
@keyframes dash{to{stroke-dashoffset:-16}}
.flight .plane{
  position:absolute;top:0;left:0;font-size:1.4rem;
  offset-path:path("M 10 100 C 140 10, 300 130, 540 34");
  offset-rotate:auto 45deg;animation:fly 8s cubic-bezier(.45,.05,.55,.95) infinite;
}
@keyframes fly{
  0%{offset-distance:0%;opacity:0} 6%{opacity:1}
  78%{offset-distance:100%;opacity:1} 84%,100%{offset-distance:100%;opacity:0}
}

/* ── right: envelope ────────────────────── */
.panel-form{position:relative}
.giant-mark{position:absolute;top:-90px;right:-70px;width:320px;opacity:.14;pointer-events:none;animation:spin 70s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.giant-mark text{font-family:var(--mono);font-size:15px;letter-spacing:3px;fill:#fff}
.giant-mark circle{stroke:#fff;fill:none;stroke-width:2}

.envelope{
  position:relative;border-radius:10px;padding:11px;
  box-shadow:0 24px 60px rgba(0,0,0,.4), 0 3px 0 rgba(255,255,255,.06) inset;
  background:repeating-linear-gradient(-45deg,var(--red) 0 14px,var(--paper) 14px 28px,var(--blue) 28px 42px,var(--paper) 42px 56px);
  background-size:200% 200%;animation:stripes 26s linear infinite;
}
@keyframes stripes{to{background-position:158.4px 158.4px}}
.envelope-inner{background:var(--paper);border-radius:5px;color:var(--ink);padding:clamp(1.4rem,3vw,2.2rem)}

.env-top{display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:1.6rem}
.par-avion{font-family:var(--mono);font-size:.68rem;letter-spacing:.24em;text-transform:uppercase;color:var(--blue);border:1.5px solid currentColor;padding:.35em .7em;border-radius:3px;align-self:center}

/* stamp with perforated edge */
.stamp{
  position:relative;flex:0 0 auto;padding:7px;background-color:#fff;
  background-image:radial-gradient(circle 2.5px,var(--paper) 96%,transparent);
  background-size:10px 10px;box-shadow:0 2px 6px rgba(23,34,79,.18);
  transition:transform .3s;
}
.stamp:hover{transform:rotate(-2.5deg) scale(1.03)}
.stamp-inner{width:88px;height:106px;background:var(--red);border:1px solid rgba(255,255,255,.55);
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.3rem;color:#fff}
.stamp-inner svg{width:34px;height:34px;fill:#fff}
.stamp-price{font-family:var(--disp);font-size:1.5rem;line-height:1}
.stamp-word{font-family:var(--mono);font-size:.55rem;letter-spacing:.3em}

/* cancellation waves over the stamp */
.stamp::after{
  content:"";position:absolute;inset:-6px -30px auto -44px;height:54px;pointer-events:none;opacity:.45;
  background:
    repeating-radial-gradient(circle at 0 8px, transparent 0 5px, var(--blue) 5px 6.5px) 0 0/100% 16px no-repeat;
  mask:linear-gradient(#000 70%,transparent);
}

/* field */
.field{position:relative;margin-bottom:1.4rem}
.field label{font-family:var(--mono);font-size:.7rem;letter-spacing:.26em;text-transform:uppercase;color:var(--ink-soft)}
.field input{
  width:100%;border:0;background:transparent;font-family:'Caveat',cursive;
  font-size:clamp(1.9rem,4vw,2.5rem);color:var(--ink);padding:.15em .1em .05em;caret-color:var(--red);
}
.field input::placeholder{color:rgba(23,34,79,.3)}
.field input:focus{outline:none}
.field-line{position:absolute;left:0;right:0;bottom:0;height:3px;background:var(--ink)}
.field-line::after{content:"";position:absolute;inset:0;background:var(--red);transform:scaleX(0);transform-origin:left;transition:transform .45s cubic-bezier(.2,.9,.3,1)}
.field:focus-within .field-line::after{transform:scaleX(1)}

.payload{font-family:var(--mono);font-size:.74rem;color:var(--ink-soft);margin-bottom:1.6rem;
  border:1px dashed rgba(23,34,79,.3);padding:.5em .8em;border-radius:4px;display:flex;gap:.6em;flex-wrap:wrap}
.payload b{color:var(--red-dark);font-weight:600}
.from-line{font-family:'Caveat',cursive;font-size:1.25rem;color:var(--ink-soft);margin-bottom:1.8rem}

/* button */
.send{
  display:inline-flex;align-items:center;gap:.7em;cursor:pointer;border:0;
  font-family:var(--mono);font-weight:600;font-size:.92rem;letter-spacing:.14em;text-transform:uppercase;
  color:#fff;background:var(--red);padding:1em 1.7em;border-radius:4px;
  box-shadow:5px 5px 0 var(--ink);transition:transform .2s,box-shadow .2s,background .2s;
}
.send:hover{transform:translate(-2px,-2px);box-shadow:8px 8px 0 var(--ink);background:#f04c33}
.send:active{transform:translate(2px,2px);box-shadow:1px 1px 0 var(--ink)}
.send-plane{display:inline-block;transition:transform .35s}
.send:hover .send-plane{transform:translateX(6px) rotate(10deg)}
.is-sending .send{background:#8f96b8;pointer-events:none;box-shadow:3px 3px 0 var(--ink)}
.is-sending .send-plane{animation:fly-away .8s ease-in forwards}
@keyframes fly-away{to{transform:translate(70px,-24px) rotate(20deg);opacity:0}}

.status{font-family:var(--mono);font-size:.78rem;margin-top:1rem;min-height:1.4em;color:var(--ink-soft)}
.status.err{color:var(--red-dark)}

/* delivered stamp */
.delivered{
  position:absolute;top:44%;left:50%;translate:-50% -50%;rotate:-9deg;
  font-family:var(--disp);font-size:clamp(1.6rem,3.4vw,2.4rem);letter-spacing:.08em;text-transform:uppercase;
  color:var(--red);border:5px double var(--red);border-radius:10px;padding:.35em .8em;
  mix-blend-mode:multiply;pointer-events:none;opacity:0;
}
.delivered.show{animation:slam .5s cubic-bezier(.2,1.4,.4,1) forwards}
@keyframes slam{0%{opacity:0;scale:1.9;filter:blur(4px)}70%{opacity:1;scale:.94}100%{opacity:1;scale:1;filter:blur(0)}}

.hint{margin-top:1.1rem;font-family:var(--mono);font-size:.7rem;color:rgba(255,255,255,.5);letter-spacing:.04em}
.hint code{color:#ffd24a}

@media (max-width:900px){
  .stage{grid-template-columns:1fr;padding-top:2.5rem}
  .giant-mark{width:220px;top:-60px;right:-40px}
  .flight{display:none}
}
@media (prefers-reduced-motion:reduce){
  *,*::before,*::after{animation:none!important;transition:none!important}
}
</style>
</head>
<body>

<!-- scrolling ticker -->
<div class="ticker" aria-hidden="true">
  <div class="ticker-track">
    <span>First-class delivery&nbsp;&nbsp;✈&nbsp;&nbsp;Par avion&nbsp;&nbsp;✈&nbsp;&nbsp;method: POST&nbsp;&nbsp;✈&nbsp;&nbsp;No stamp required&nbsp;&nbsp;✈&nbsp;&nbsp;First-class delivery&nbsp;&nbsp;✈&nbsp;&nbsp;Par avion&nbsp;&nbsp;✈&nbsp;&nbsp;method: POST&nbsp;&nbsp;✈&nbsp;&nbsp;No stamp required&nbsp;&nbsp;✈&nbsp;&nbsp;</span>
    <span>First-class delivery&nbsp;&nbsp;✈&nbsp;&nbsp;Par avion&nbsp;&nbsp;✈&nbsp;&nbsp;method: POST&nbsp;&nbsp;✈&nbsp;&nbsp;No stamp required&nbsp;&nbsp;✈&nbsp;&nbsp;First-class delivery&nbsp;&nbsp;✈&nbsp;&nbsp;Par avion&nbsp;&nbsp;✈&nbsp;&nbsp;method: POST&nbsp;&nbsp;✈&nbsp;&nbsp;No stamp required&nbsp;&nbsp;✈&nbsp;&nbsp;</span>
  </div>
</div>
<main class="stage">

  <!-- left: the pitch -->
  <section>
    <p class="kicker">Form Nº 001 · HTTP Request</p>
    <h1 class="headline">
      <span class="h-line">Mail us</span>
      <span class="h-line h-outline">your name</span>
      <span class="h-line">via <em class="post-chip">POST</em></span>
    </h1>
    <p class="lede">One field, one request. Fill in the envelope, hit send, and your name
      goes out in the body of a <code>POST</code> — <code>name=you</code>, straight to the server.</p>

    <dl class="specs">
      <div class="spec"><dt>Action</dt><dd>/submit-name</dd></div>
      <div class="spec"><dt>Method</dt><dd>POST</dd></div>
      <div class="spec"><dt>Payload field</dt><dd>name</dd></div>
      <div class="spec"><dt>Postage</dt><dd>Paid ✓</dd></div>
    </dl>

    <div class="flight" aria-hidden="true">
      <svg viewBox="0 0 560 130" preserveAspectRatio="none">
        <path class="route" d="M 10 100 C 140 10, 300 130, 540 34"/>
      </svg>
      <span class="plane">✈️</span>
    </div>
  </section>

  <!-- right: the envelope -->
  <section class="panel-form">
    <svg class="giant-mark" viewBox="0 0 300 300" aria-hidden="true">
      <defs><path id="ring" d="M150,150 m-112,0 a112,112 0 1,1 224,0 a112,112 0 1,1 -224,0"/></defs>
      <circle cx="150" cy="150" r="130"/><circle cx="150" cy="150" r="94"/>
      <text><textPath href="#ring">EXPRESS MAIL · PAR AVION · FIRST CLASS · EXPRESS MAIL ·</textPath></text>
    </svg>

    <div class="envelope" id="envelope">
      <div class="envelope-inner">

        <div class="env-top">
          <span class="par-avion">✈ Par Avion — By Air Mail</span>
          <div class="stamp">
            <div class="stamp-inner">
              <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
              <span class="stamp-price">45</span>
              <span class="stamp-word">POSTES</span>
            </div>
          </div>
        </div>

        <!-- ★ the actual POST form — works without JS ★ -->
        <form id="nameForm" action="/save-name.php" method="post">
          <div class="field">
            <label for="name">To — your name</label>
            <input id="name" name="name" type="text" required minlength="2" maxlength="60"
                   autocomplete="name" placeholder="Write your name here…">
            <span class="field-line"></span>
          </div>

          <p class="payload">body: <b id="payload">name=</b></p>
          <p class="from-line">from: <span id="fromHost">your browser</span></p>

          <button class="send" type="submit">
            <span id="sendLabel">Send it</span> <span class="send-plane" aria-hidden="true">✈</span>
          </button>
          <p class="status" id="status" role="status" aria-live="polite"></p>
        </form>

        <div class="delivered" id="delivered" aria-hidden="true">Delivered ✓</div>
      </div>
    </div>

    <p class="hint">Point <code>action="/submit-name"</code> at your endpoint — the server receives <code>name</code> in the POST body.</p>
  </section>
</main>

<script>
const form = document.getElementById('nameForm');
const input = document.getElementById('name');
const statusEl = document.getElementById('status');
const payloadEl = document.getElementById('payload');
const delivered = document.getElementById('delivered');
const sendLabel = document.getElementById('sendLabel');

/* live payload preview */
input.addEventListener('input', () =>
  payloadEl.textContent = 'name=' + encodeURIComponent(input.value));

/* fill in the "from" line */
try { document.getElementById('fromHost').textContent = location.host || 'local file'; } catch(e){}

form.addEventListener('submit', async (e) => {
  e.preventDefault();                       // remove this line for a classic full-page POST
  if (!form.checkValidity()) { form.reportValidity(); return; }

  delivered.classList.remove('show');
  form.classList.add('is-sending');
  statusEl.classList.remove('err');
  statusEl.textContent = 'Dispatching…';
  sendLabel.textContent = 'Sending…';

  let ok = false, networkErr = false;
  try {
    const res = await fetch(form.action, { method: 'POST', body: new FormData(form) });
    ok = res.ok;
  } catch { networkErr = true; ok = true; } // demo mode: no server → treat as delivered

  form.classList.remove('is-sending');
  sendLabel.textContent = 'Send it';
  delivered.classList.add('show');

  statusEl.textContent = networkErr
    ? 'Demo delivery ✓ — hook the form to a real endpoint.'
    : (ok ? 'Delivered to ' + form.action + ' ✓' : 'Bounced — server replied with an error.');
  if (!ok) statusEl.classList.add('err');
});
</script>
</body>
</html>
