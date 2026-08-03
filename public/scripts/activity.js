(() => {
 let pending = 0;
 let timer;
 const overlay = document.createElement('div');
 overlay.className = 'activity-overlay';
 overlay.setAttribute('role', 'status');
 overlay.setAttribute('aria-live', 'polite');
 overlay.setAttribute('aria-hidden', 'true');
 overlay.innerHTML = '<div class="activity-box"><span class="activity-spinner" aria-hidden="true"></span><span>Carregando...</span></div>';
 document.body.appendChild(overlay);

 const show = () => {
  pending++;
  clearTimeout(timer);
  timer = setTimeout(() => {
   if (pending > 0) {
    overlay.classList.add('is-visible');
    overlay.setAttribute('aria-hidden', 'false');
   }
  }, 140);
 };

 const hide = () => {
  pending = Math.max(0, pending - 1);
  if (pending === 0) {
   clearTimeout(timer);
   overlay.classList.remove('is-visible');
   overlay.setAttribute('aria-hidden', 'true');
  }
 };

 document.addEventListener('submit', event => {
  if (!event.defaultPrevented) show();
 });

 document.addEventListener('click', event => {
  const link = event.target.closest('a[href]');
  if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target === '_blank' || link.hasAttribute('download')) return;
  const url = new URL(link.href, location.href);
  if (url.origin !== location.origin || (url.pathname === location.pathname && url.search === location.search && url.hash)) return;
  show();
 });

 const nativeFetch = window.fetch;
 window.fetch = (...args) => {
  show();
  return nativeFetch(...args).finally(hide);
 };

 window.addEventListener('pageshow', () => {
  pending = 0;
  hide();
 });
})();
