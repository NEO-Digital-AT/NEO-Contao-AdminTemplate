/* NEOcms (Contao-Backend): Seitenleiste ein-/ausklappen wie in Outlook.
   Lädt nur im Backend (contao.backend.custom_js) und im <head> — die Klasse
   steht damit schon vor dem ersten Zeichnen am <html>, nichts springt.
   Zustand pro Browser in localStorage; ohne Speicher bleibt die Leiste offen.
   Unter 992 px blendet backend.css den Knopf aus — dort gilt Contaos Burger. */
(() => {
  const KEY = 'neocms-nav-collapsed';
  const CLASS = 'neo-nav-collapsed';
  const root = document.documentElement;

  const stored = () => {
    try { return localStorage.getItem(KEY) === '1'; } catch (e) { return false; }
  };

  const sync = () => {
    const collapsed = root.classList.contains(CLASS);
    document.querySelectorAll('.neo-nav-toggle').forEach((btn) => {
      const title = collapsed ? btn.dataset.titleShow : btn.dataset.titleHide;
      btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
      btn.setAttribute('title', title);
      const label = btn.querySelector('.invisible');
      if (label) label.textContent = title;
    });
  };

  root.classList.toggle(CLASS, stored());

  document.addEventListener('click', (event) => {
    if (!event.target.closest('.neo-nav-toggle')) return;
    const collapsed = root.classList.toggle(CLASS);
    try { localStorage.setItem(KEY, collapsed ? '1' : '0'); } catch (e) { /* privater Modus */ }
    sync();
  });

  document.addEventListener('DOMContentLoaded', sync);
  // Contao lädt Seiten per Turbo nach: Kopfzeile neu → Beschriftung angleichen
  document.addEventListener('turbo:load', sync);
})();
