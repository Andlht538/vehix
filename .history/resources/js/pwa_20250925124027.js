// resources/js/pwa.js
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')  // ✅ Chemin correct
      .then((registration) => {
        console.log('ServiceWorker enregistré avec succès:', registration.scope);
      })
      .catch((error) => {
        console.log('Échec de l’enregistrement du ServiceWorker:', error);
      });
  });
}