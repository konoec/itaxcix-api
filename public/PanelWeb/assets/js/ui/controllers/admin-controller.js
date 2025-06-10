// Controlador para el panel de administración
document.addEventListener("DOMContentLoaded", () => {
  console.log("Inicializando panel de administración...");

  // Configurar el botón de cerrar sesión usando RouteGuard
  setupLogout();

  // Mostrar el nombre de usuario si está disponible
  displayUsername();

  console.log("Panel de administración inicializado correctamente");
});

// Configurar el botón de cerrar sesión
function setupLogout() {
  const logoutBtn = document.getElementById("logout-btn");

  if (logoutBtn) {
    console.log("Botón de logout encontrado, configurando evento...");
    logoutBtn.addEventListener("click", () => {
      console.log("Botón de logout clickeado");
      routeGuard.logout(); // Usa RouteGuard para limpiar sesión y redirigir
    });
    console.log("Botón de logout configurado correctamente");
  } else {
    console.warn("No se encontró el botón de logout con ID 'logout-btn'");
    const logoutBtns = document.querySelectorAll(".btn-logout");
    if (logoutBtns.length > 0) {
      console.log("Botón de logout encontrado por clase, configurando evento...");
      logoutBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
          console.log("Botón de logout clickeado");
          routeGuard.logout();
        });
      });
      console.log("Botones de logout configurados correctamente");
    } else {
      console.error("No se encontró ningún botón de logout en la página");
    }
  }
}

// Mostrar el nombre de usuario
function displayUsername() {
  const username = sessionStorage.getItem("username");
  const userDisplay = document.getElementById("user-display");
  if (userDisplay && username) {
    userDisplay.textContent = username;
    console.log(`Nombre de usuario mostrado: ${username}`);
  }
}
