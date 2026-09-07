/**
 * BMB3 Accessibility Widget
 * הזרק דרך WordPress > Appearance > Theme File Editor > footer.php
 * או דרך פלאגין "Insert Headers and Footers"
 */

(function () {
  if (document.getElementById('bmb3-a11y-panel')) return;

  /* ── סגנונות ── */
  const style = document.createElement('style');
  style.textContent = `
    #bmb3-a11y-btn {
      position: fixed;
      bottom: 24px;
      left: 24px;
      z-index: 99999;
      width: 68px;
      height: 68px;
      border-radius: 50%;
      background: #1fa873;
      color: #fff;
      border: 3px solid #34e29f;
      cursor: pointer;
      box-shadow: 0 6px 20px rgba(31,168,115,0.45);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s, transform 0.15s;
      font-size: 28px;
    }
    #bmb3-a11y-btn:hover { background: #2ecc8f; transform: scale(1.08); }
    #bmb3-a11y-btn:focus-visible { outline: 3px solid #f59e0b; outline-offset: 3px; }

    #bmb3-a11y-panel {
      position: fixed;
      bottom: 84px;
      left: 24px;
      z-index: 99998;
      width: 270px;
      background: #fff;
      border: 1px solid #d1d5db;
      border-radius: 14px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.14);
      padding: 18px 16px 14px;
      font-family: 'Segoe UI', Arial, sans-serif;
      direction: rtl;
      display: none;
      animation: bmb3FadeIn 0.18s ease;
    }
    #bmb3-a11y-panel.open { display: block; }

    @keyframes bmb3FadeIn {
      from { opacity: 0; transform: translateY(8px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    #bmb3-a11y-panel h3 {
      margin: 0 0 14px;
      font-size: 14px;
      font-weight: 700;
      color: #111827;
      display: flex;
      align-items: center;
      gap: 6px;
      border-bottom: 1px solid #e5e7eb;
      padding-bottom: 10px;
    }

    .bmb3-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    .bmb3-row label {
      font-size: 13px;
      color: #374151;
      font-weight: 500;
    }
    .bmb3-controls { display: flex; align-items: center; gap: 6px; }
    .bmb3-controls button {
      width: 28px; height: 28px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      background: #f9fafb;
      cursor: pointer;
      font-size: 14px;
      font-weight: 700;
      color: #374151;
      transition: background 0.15s;
      display: flex; align-items: center; justify-content: center;
    }
    .bmb3-controls button:hover { background: #e5e7eb; }
    .bmb3-controls span {
      font-size: 12px; color: #6b7280; min-width: 28px; text-align: center;
    }

    .bmb3-toggle-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    .bmb3-toggle-row label { font-size: 13px; color: #374151; font-weight: 500; }

    .bmb3-switch {
      position: relative; width: 38px; height: 22px;
    }
    .bmb3-switch input { opacity: 0; width: 0; height: 0; }
    .bmb3-slider {
      position: absolute; inset: 0;
      background: #d1d5db; border-radius: 22px;
      transition: background 0.2s; cursor: pointer;
    }
    .bmb3-slider:before {
      content: '';
      position: absolute;
      width: 16px; height: 16px;
      left: 3px; top: 3px;
      background: #fff; border-radius: 50%;
      transition: transform 0.2s;
    }
    .bmb3-switch input:checked + .bmb3-slider { background: #1fa873; }
    .bmb3-switch input:checked + .bmb3-slider:before { transform: translateX(16px); }

    #bmb3-reset-btn {
      width: 100%;
      margin-top: 4px;
      padding: 7px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      background: #f3f4f6;
      color: #374151;
      font-size: 12px;
      cursor: pointer;
      transition: background 0.15s;
    }
    #bmb3-reset-btn:hover { background: #e5e7eb; }

    /* אפקטים */
    body.bmb3-high-contrast { filter: contrast(1.6); }
    body.bmb3-big-cursor, body.bmb3-big-cursor * { cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Cpath d='M8 2 L8 26 L14 20 L18 30 L21 29 L17 19 L26 19 Z' fill='black' stroke='white' stroke-width='1.5'/%3E%3C/svg%3E") 0 0, auto !important; }
    body.bmb3-no-animations *, body.bmb3-no-animations *::before, body.bmb3-no-animations *::after {
      animation-duration: 0.001ms !important;
      transition-duration: 0.001ms !important;
    }
  `;
  document.head.appendChild(style);

  /* ── HTML ── */
  const btn = document.createElement('button');
  btn.id = 'bmb3-a11y-btn';
  btn.setAttribute('aria-label', 'פתח תפריט נגישות');
  btn.setAttribute('aria-expanded', 'false');
  btn.setAttribute('aria-controls', 'bmb3-a11y-panel');
  btn.innerHTML = `<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="5" r="1.5"/><path d="M5 8h14M12 8v6m-4 2l1-4m6 4l-1-4"/></svg>`;

  const panel = document.createElement('div');
  panel.id = 'bmb3-a11y-panel';
  panel.setAttribute('role', 'dialog');
  panel.setAttribute('aria-label', 'תפריט נגישות');
  panel.innerHTML = `
    <h3>♿ נגישות</h3>

    <div class="bmb3-row">
      <label>גודל גופן</label>
      <div class="bmb3-controls">
        <button id="bmb3-font-down" aria-label="הקטן גופן">−</button>
        <span id="bmb3-font-val">100%</span>
        <button id="bmb3-font-up" aria-label="הגדל גופן">+</button>
      </div>
    </div>

    <div class="bmb3-row">
      <label>מרווח שורות</label>
      <div class="bmb3-controls">
        <button id="bmb3-line-down" aria-label="הקטן מרווח">−</button>
        <span id="bmb3-line-val">1.5</span>
        <button id="bmb3-line-up" aria-label="הגדל מרווח">+</button>
      </div>
    </div>

    <div class="bmb3-toggle-row">
      <label for="bmb3-contrast">ניגודיות גבוהה</label>
      <label class="bmb3-switch">
        <input type="checkbox" id="bmb3-contrast" role="switch" aria-checked="false">
        <span class="bmb3-slider"></span>
      </label>
    </div>

    <div class="bmb3-toggle-row">
      <label for="bmb3-cursor">סמן גדול</label>
      <label class="bmb3-switch">
        <input type="checkbox" id="bmb3-cursor" role="switch" aria-checked="false">
        <span class="bmb3-slider"></span>
      </label>
    </div>

    <div class="bmb3-toggle-row">
      <label for="bmb3-motion">הפחת אנימציות</label>
      <label class="bmb3-switch">
        <input type="checkbox" id="bmb3-motion" role="switch" aria-checked="false">
        <span class="bmb3-slider"></span>
      </label>
    </div>

    <button id="bmb3-reset-btn">↺ אפס הכל</button>
  `;

  document.body.appendChild(btn);
  document.body.appendChild(panel);

  /* ── לוגיקה ── */
  let fontSize = 100;   // %
  let lineHeight = 1.5;

  function updateFont() {
    // scale all page text except the accessibility panel itself.
    var scale = fontSize / 100;
    var sel = 'h1,h2,h3,h4,h5,h6,p,span,a,li,div,button,label,strong,em';
    document.querySelectorAll(sel).forEach(function(el){
      if (el.closest('#bmb3-a11y-panel') || el.closest('#bmb3-a11y-btn')) return;
      if (!el.dataset.bmb3Fs) {
        var base = parseFloat(window.getComputedStyle(el).fontSize);
        el.dataset.bmb3Fs = base;
      }
      el.style.fontSize = (parseFloat(el.dataset.bmb3Fs) * scale) + 'px';
    });
    document.getElementById('bmb3-font-val').textContent = fontSize + '%';
  }
  function updateLine() {
    document.querySelectorAll('p,li,span,div').forEach(function(el){
      if (el.closest('#bmb3-a11y-panel') || el.closest('#bmb3-a11y-btn')) return;
      el.style.lineHeight = lineHeight;
    });
    document.getElementById('bmb3-line-val').textContent = lineHeight.toFixed(1);
  }

  btn.addEventListener('click', () => {
    const open = panel.classList.toggle('open');
    btn.setAttribute('aria-expanded', open);
  });

  document.addEventListener('click', (e) => {
    if (!panel.contains(e.target) && e.target !== btn) {
      panel.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      panel.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
      btn.focus();
    }
  });

  document.getElementById('bmb3-font-up').addEventListener('click', () => {
    if (fontSize < 150) { fontSize += 10; updateFont(); }
  });
  document.getElementById('bmb3-font-down').addEventListener('click', () => {
    if (fontSize > 70) { fontSize -= 10; updateFont(); }
  });

  document.getElementById('bmb3-line-up').addEventListener('click', () => {
    if (lineHeight < 2.5) { lineHeight = +(lineHeight + 0.1).toFixed(1); updateLine(); }
  });
  document.getElementById('bmb3-line-down').addEventListener('click', () => {
    if (lineHeight > 1.0) { lineHeight = +(lineHeight - 0.1).toFixed(1); updateLine(); }
  });

  document.getElementById('bmb3-contrast').addEventListener('change', function () {
    document.body.classList.toggle('bmb3-high-contrast', this.checked);
    this.setAttribute('aria-checked', this.checked);
  });
  document.getElementById('bmb3-cursor').addEventListener('change', function () {
    document.body.classList.toggle('bmb3-big-cursor', this.checked);
    this.setAttribute('aria-checked', this.checked);
  });
  document.getElementById('bmb3-motion').addEventListener('change', function () {
    document.body.classList.toggle('bmb3-no-animations', this.checked);
    this.setAttribute('aria-checked', this.checked);
  });

  document.getElementById('bmb3-reset-btn').addEventListener('click', () => {
    fontSize = 100; lineHeight = 1.5;
    // clear all inline overrides we added
    document.querySelectorAll('[data-bmb3-fs]').forEach(function(el){
      el.style.fontSize = '';
    });
    document.querySelectorAll('p,li,span,div').forEach(function(el){
      if (el.closest('#bmb3-a11y-panel') || el.closest('#bmb3-a11y-btn')) return;
      el.style.lineHeight = '';
    });
    document.getElementById('bmb3-font-val').textContent = '100%';
    document.getElementById('bmb3-line-val').textContent = '1.5';
    ['bmb3-contrast','bmb3-cursor','bmb3-motion'].forEach(id => {
      const el = document.getElementById(id);
      el.checked = false;
      el.setAttribute('aria-checked', 'false');
    });
    document.body.classList.remove('bmb3-high-contrast','bmb3-big-cursor','bmb3-no-animations');
  });

})();
