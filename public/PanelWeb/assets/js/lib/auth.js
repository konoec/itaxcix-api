// Función para guardar la sesión con todos los datos de autenticación
function saveSession(authData) {
  if (!authData || !authData.token) {
    console.error("Datos de autenticación inválidos")
    return false
  }
  
  // Guardar datos básicos de sesión
  sessionStorage.setItem("isLoggedIn", "true")
  sessionStorage.setItem("loginTime", Date.now().toString())
  
  // Guardar datos de autenticación
  sessionStorage.setItem("authToken", authData.token)
  sessionStorage.setItem("userId", authData.userId.toString())
  sessionStorage.setItem("documentValue", authData.documentValue)
  
  // Guardar información del nombre del usuario
  if (authData.firstName) {
    sessionStorage.setItem("firstName", authData.firstName)
  }
  if (authData.lastName) {
    sessionStorage.setItem("lastName", authData.lastName)
  }
  if (authData.firstName || authData.lastName) {
    const fullName = `${authData.firstName || ""} ${authData.lastName || ""}`.trim()
    sessionStorage.setItem("userFullName", fullName)
  }
  if (authData.rating) {
    sessionStorage.setItem("userRating", authData.rating.toString())
  }
  
  // Guardar roles y permisos
  if (authData.roles && authData.roles.length > 0) {
    sessionStorage.setItem("userRoles", JSON.stringify(authData.roles))
  }
  
  if (authData.permissions && authData.permissions.length > 0) {
    sessionStorage.setItem("userPermissions", JSON.stringify(authData.permissions))
  }
  
  // Guardar disponibilidad si existe
  if (authData.availability !== null) {
    sessionStorage.setItem("userAvailability", authData.availability.toString())
  }
  
  return true
}

// Función para cerrar sesión
function logout() {
  console.log("Ejecutando función logout desde auth.js...")

  // Limpiar todos los datos de sesión
  sessionStorage.removeItem("isLoggedIn")
  sessionStorage.removeItem("loginTime")
  sessionStorage.removeItem("authToken")
  sessionStorage.removeItem("userId")
  sessionStorage.removeItem("documentValue")
  sessionStorage.removeItem("userRoles")
  sessionStorage.removeItem("userPermissions")
  sessionStorage.removeItem("userAvailability")
  
  // Limpiar información del nombre del usuario
  sessionStorage.removeItem("firstName")
  sessionStorage.removeItem("lastName")
  sessionStorage.removeItem("userFullName")
  sessionStorage.removeItem("userRating")

  console.log("Sesión eliminada, redirigiendo...")

  // Redirigir a la página de login
  window.location.replace("../../index.html")

  // También intentar con href por si replace no funciona
  if (window.location.pathname.includes("/pages/")) {
    window.location.href = "../../index.html"
  } else {
    window.location.href = "/index.html"
  }
}

// Función para verificar si el usuario está autenticado
function isAuthenticated() {
  const isLoggedIn = sessionStorage.getItem("isLoggedIn")
  const loginTime = sessionStorage.getItem("loginTime")
  const token = sessionStorage.getItem("authToken")

  // Verificar que exista el token además de la sesión
  if (isLoggedIn !== "true" || !loginTime || !token) return false

  // Verificar si la sesión ha expirado (30 minutos)
  const now = Date.now()
  const sessionTime = Number.parseInt(loginTime)
  const sessionDuration = 30 * 60 * 1000 // 30 minutos en milisegundos

  return now - sessionTime < sessionDuration
}

// Función para obtener el token
function getAuthToken() {
  return sessionStorage.getItem("authToken")
}

// Función para verificar si el usuario tiene un rol específico
function hasRole(roleName) {
  const rolesJson = sessionStorage.getItem("userRoles")
  if (!rolesJson) return false
  
  try {
    const roles = JSON.parse(rolesJson)
    return roles.includes(roleName)
  } catch (e) {
    console.error("Error al verificar roles:", e)
    return false
  }
}

// Función para verificar si el usuario tiene un permiso específico
function hasPermission(permissionName) {
  const permissionsJson = sessionStorage.getItem("userPermissions")
  if (!permissionsJson) return false
  
  try {
    const permissions = JSON.parse(permissionsJson)
    return permissions.includes(permissionName)
  } catch (e) {
    console.error("Error al verificar permisos:", e)
    return false
  }
}

// Función para obtener información del usuario actual
function getUserInfo() {
  return {
    userId: sessionStorage.getItem("userId"),
    documentValue: sessionStorage.getItem("documentValue"),
    firstName: sessionStorage.getItem("firstName"),
    lastName: sessionStorage.getItem("lastName"),
    fullName: sessionStorage.getItem("userFullName"),
    rating: parseFloat(sessionStorage.getItem("userRating")) || 0,
    roles: JSON.parse(sessionStorage.getItem("userRoles") || "[]"),
    permissions: JSON.parse(sessionStorage.getItem("userPermissions") || "[]"),
    availability: sessionStorage.getItem("userAvailability") === "true"
  }
}

// Función para obtener el nombre completo del usuario
function getUserFullName() {
  const fullName = sessionStorage.getItem("userFullName")
  const firstName = sessionStorage.getItem("firstName")
  const lastName = sessionStorage.getItem("lastName")
  
  if (fullName && fullName.trim() !== "") {
    return fullName.trim()
  }
  
  if (firstName || lastName) {
    return `${firstName || ""} ${lastName || ""}`.trim()
  }
  
  return null
}

// Función para obtener el rating del usuario
function getUserRating() {
  const rating = sessionStorage.getItem("userRating")
  return rating ? parseFloat(rating) : 0
}

// Exponer la función de logout globalmente
window.doLogout = () => {
  console.log("Ejecutando doLogout global...")
  logout()
}