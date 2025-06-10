#!/bin/bash

# Script para subir cambios automáticamente a GitHub
# Archivo: subir.sh
# Uso: ./subir.sh "mensaje del commit"

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para mostrar mensajes con color
print_message() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Verificar si estamos en un repositorio git
if [ ! -d ".git" ]; then
    print_error "Este directorio no es un repositorio Git."
    print_message "Inicializando repositorio Git..."
    git init
    git remote add origin https://github.com/tu-usuario/PanelWeb.git
    print_warning "¡IMPORTANTE! Actualiza la URL del repositorio en este script."
fi

# Mensaje del commit (usar parámetro o mensaje por defecto)
if [ -z "$1" ]; then
    COMMIT_MESSAGE="Actualización automática - $(date '+%Y-%m-%d %H:%M:%S')"
else
    COMMIT_MESSAGE="$1"
fi

print_message "Iniciando proceso de subida a GitHub..."
echo "=================================="

# Mostrar estado actual
print_message "Estado actual del repositorio:"
git status --short

echo ""

# Agregar todos los archivos
print_message "Agregando archivos al staging area..."
git add .

# Verificar si hay cambios para commitear
if git diff --staged --quiet; then
    print_warning "No hay cambios para commitear."
    exit 0
fi

# Mostrar archivos que se van a commitear
print_message "Archivos que se van a commitear:"
git diff --staged --name-only

echo ""

# Hacer commit
print_message "Creando commit con mensaje: '$COMMIT_MESSAGE'"
git commit -m "$COMMIT_MESSAGE"

if [ $? -ne 0 ]; then
    print_error "Error al crear el commit."
    exit 1
fi

# Verificar rama actual
CURRENT_BRANCH=$(git branch --show-current)
print_message "Rama actual: $CURRENT_BRANCH"

# Subir cambios
print_message "Subiendo cambios a GitHub (rama: $CURRENT_BRANCH)..."
git push origin $CURRENT_BRANCH

if [ $? -eq 0 ]; then
    print_success "¡Cambios subidos exitosamente a GitHub!"
    print_success "Commit: $COMMIT_MESSAGE"
    print_success "Rama: $CURRENT_BRANCH"
else
    print_error "Error al subir cambios a GitHub."
    print_message "Intentando hacer push con --set-upstream..."
    git push --set-upstream origin $CURRENT_BRANCH
    
    if [ $? -eq 0 ]; then
        print_success "¡Cambios subidos exitosamente con --set-upstream!"
    else
        print_error "No se pudieron subir los cambios. Verifica tu conexión y credenciales."
        exit 1
    fi
fi

echo "=================================="
print_success "Proceso completado."

# Mostrar resumen final
echo ""
print_message "Resumen:"
echo "- Commit: $COMMIT_MESSAGE"
echo "- Rama: $CURRENT_BRANCH"
echo "- Archivos actualizados: $(git diff HEAD~1 --name-only | wc -l)"
print_message "Ver cambios en: https://github.com/tu-usuario/PanelWeb/commit/$(git rev-parse HEAD)"