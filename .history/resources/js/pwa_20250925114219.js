// resources/js/pwa.js
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then((registration) => {
        console.log('ServiceWorker enregistré avec succès:', registration.scope);
      })
      .catch((error) => {
        console.log('Échec de l’enregistrement du ServiceWorker:', error);
      });
  });
}

// Optionnel : Gestion des notifications ou autres fonctionnalités PWA