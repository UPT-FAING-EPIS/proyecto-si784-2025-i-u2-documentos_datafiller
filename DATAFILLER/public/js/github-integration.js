document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const githubUser = document.getElementById('githubUser');
    const githubRepo = document.getElementById('githubRepo');
    const githubPath = document.getElementById('githubPath');
    const githubFiles = document.getElementById('githubFiles');
    const loadRepos = document.getElementById('loadRepos');
    const loadFiles = document.getElementById('loadFiles');
    const previewGithubFile = document.getElementById('previewGithubFile');
    const loadGithubFile = document.getElementById('loadGithubFile');
    const githubPreview = document.getElementById('githubPreview');
    const previewContent = document.getElementById('previewContent');
    const previewInfo = document.getElementById('previewInfo');
    const scriptTextarea = document.getElementById('script');

    // GitHub API base URL
    const GITHUB_API_BASE = 'https://api.github.com';

    // Rate limiting info
    let rateLimitRemaining = 60;
    let rateLimitReset = null;

    // Event Listeners
    githubUser.addEventListener('input', debounce(handleUserChange, 500));
    githubRepo.addEventListener('change', handleRepoChange);
    githubPath.addEventListener('input', debounce(handlePathChange, 300));
    loadRepos.addEventListener('click', loadRepositories);
    loadFiles.addEventListener('click', loadSqlFiles);
    previewGithubFile.addEventListener('click', previewFile);
    loadGithubFile.addEventListener('click', loadFileToTextarea);

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Handle user input change
    function handleUserChange() {
        const user = githubUser.value.trim();
        
        if (user.length >= 3) {
            enableElement(loadRepos);
            resetDownstreamElements(['repo', 'path', 'files']);
        } else {
            disableElement(loadRepos);
            resetAllDownstream();
        }
    }

    // Handle repo selection change
    function handleRepoChange() {
        const repo = githubRepo.value;
        
        if (repo) {
            enableElement(githubPath);
            enableElement(loadFiles);
            resetDownstreamElements(['files']);
        } else {
            disableElement(githubPath);
            disableElement(loadFiles);
            resetDownstreamElements(['files']);
        }
    }

    // Handle path input change
    function handlePathChange() {
        resetDownstreamElements(['files']);
    }

    // Load repositories for a user
    async function loadRepositories() {
        const user = githubUser.value.trim();
        
        if (!user) {
            showNotification('❌ Por favor ingrese un usuario de GitHub', 'error');
            return;
        }

        setLoadingState(loadRepos, true);
        
        try {
            const response = await fetch(`${GITHUB_API_BASE}/users/${user}/repos?sort=updated&per_page=100`);
            
            updateRateLimit(response);
            
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${getErrorMessage(response.status)}`);
            }

            const repos = await response.json();
            
            // Clear and populate repo select
            githubRepo.innerHTML = '<option value="">Selecciona un repositorio</option>';
            
            if (repos.length === 0) {
                githubRepo.innerHTML = '<option value="">No se encontraron repositorios públicos</option>';
                showNotification('ℹ️ No se encontraron repositorios públicos para este usuario', 'info');
            } else {
                repos.forEach(repo => {
                    const option = document.createElement('option');
                    option.value = repo.name;
                    option.textContent = `${repo.name} ${repo.description ? '- ' + repo.description.substring(0, 50) + '...' : ''}`;
                    githubRepo.appendChild(option);
                });
                
                enableElement(githubRepo);
                showNotification(`✅ ${repos.length} repositorios cargados`, 'success');
            }

        } catch (error) {
            console.error('Error loading repositories:', error);
            showNotification(`❌ Error cargando repositorios: ${error.message}`, 'error');
            githubRepo.innerHTML = '<option value="">Error cargando repositorios</option>';
        } finally {
            setLoadingState(loadRepos, false);
        }
    }

    // Load SQL files from repository
    async function loadSqlFiles() {
        const user = githubUser.value.trim();
        const repo = githubRepo.value;
        const path = githubPath.value.trim();

        if (!user || !repo) {
            showNotification('❌ Seleccione usuario y repositorio primero', 'error');
            return;
        }

        setLoadingState(loadFiles, true);

        try {
            // Build API URL
            let apiUrl = `${GITHUB_API_BASE}/repos/${user}/${repo}/contents`;
            if (path) {
                apiUrl += `/${path}`;
            }

            const response = await fetch(apiUrl);
            updateRateLimit(response);

            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${getErrorMessage(response.status)}`);
            }

            const contents = await response.json();
            
            // Filter SQL files
            const sqlFiles = Array.isArray(contents) 
                ? contents.filter(item => 
                    item.type === 'file' && 
                    (item.name.toLowerCase().endsWith('.sql') || 
                     item.name.toLowerCase().endsWith('.bak') ||
                     item.name.toLowerCase().endsWith('.json'))
                  )
                : (contents.type === 'file' && 
                   (contents.name.toLowerCase().endsWith('.sql') || 
                    contents.name.toLowerCase().endsWith('.bak') ||
                    contents.name.toLowerCase().endsWith('.json')) 
                   ? [contents] : []);

            // Populate files select
            githubFiles.innerHTML = '<option value="">Selecciona un archivo</option>';

            if (sqlFiles.length === 0) {
                githubFiles.innerHTML = '<option value="">No se encontraron archivos .sql, .bak o .json</option>';
                showNotification('ℹ️ No se encontraron archivos de base de datos en esta ruta', 'info');
            } else {
                sqlFiles.forEach(file => {
                    const option = document.createElement('option');
                    option.value = file.download_url;
                    option.dataset.name = file.name;
                    option.dataset.size = file.size;
                    option.textContent = `${file.name} (${formatFileSize(file.size)})`;
                    githubFiles.appendChild(option);
                });

                enableElement(githubFiles);
                enableElement(previewGithubFile);
                showNotification(`✅ ${sqlFiles.length} archivo(s) encontrado(s)`, 'success');
            }

            // Also show directories for navigation
            const directories = Array.isArray(contents) 
                ? contents.filter(item => item.type === 'dir')
                : [];

            if (directories.length > 0) {
                const dirGroup = document.createElement('optgroup');
                dirGroup.label = '📁 Directorios (copia la ruta)';
                directories.forEach(dir => {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = `📁 ${dir.name}/`;
                    option.disabled = true;
                    dirGroup.appendChild(option);
                });
                githubFiles.appendChild(dirGroup);
            }

        } catch (error) {
            console.error('Error loading files:', error);
            showNotification(`❌ Error cargando archivos: ${error.message}`, 'error');
            githubFiles.innerHTML = '<option value="">Error cargando archivos</option>';
        } finally {
            setLoadingState(loadFiles, false);
        }
    }

    // Preview file content
    async function previewFile() {
        const selectedOption = githubFiles.options[githubFiles.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            showNotification('❌ Selecciona un archivo primero', 'error');
            return;
        }

        const downloadUrl = selectedOption.value;
        const fileName = selectedOption.dataset.name;
        const fileSize = selectedOption.dataset.size;

        setLoadingState(previewGithubFile, true);

        try {
            const response = await fetch(downloadUrl);
            
            if (!response.ok) {
                throw new Error(`Error ${response.status}: No se pudo cargar el archivo`);
            }

            const content = await response.text();
            
            // Show preview
            previewInfo.textContent = `${fileName} (${formatFileSize(fileSize)})`;
            previewContent.textContent = content.length > 5000 
                ? content.substring(0, 5000) + '\n\n... (archivo truncado, mostrando primeros 5000 caracteres)'
                : content;
            
            githubPreview.style.display = 'block';
            enableElement(loadGithubFile);
            
            showNotification('✅ Vista previa cargada', 'success');

        } catch (error) {
            console.error('Error previewing file:', error);
            showNotification(`❌ Error cargando vista previa: ${error.message}`, 'error');
        } finally {
            setLoadingState(previewGithubFile, false);
        }
    }

    // Load file content to textarea
    async function loadFileToTextarea() {
        const selectedOption = githubFiles.options[githubFiles.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            showNotification('❌ Selecciona un archivo primero', 'error');
            return;
        }

        const downloadUrl = selectedOption.value;
        const fileName = selectedOption.dataset.name;

        setLoadingState(loadGithubFile, true);

        try {
            const response = await fetch(downloadUrl);
            
            if (!response.ok) {
                throw new Error(`Error ${response.status}: No se pudo cargar el archivo`);
            }

            const content = await response.text();
            
            // Load content to textarea
            scriptTextarea.value = content;
            
            // Update status using shared function
            if (window.updateScriptStatus) {
                window.updateScriptStatus(content, fileName);
            }
            
            // Scroll to textarea
            scriptTextarea.scrollIntoView({ behavior: 'smooth' });
            
            showNotification(`✅ Archivo ${fileName} cargado exitosamente`, 'success');

        } catch (error) {
            console.error('Error loading file:', error);
            showNotification(`❌ Error cargando archivo: ${error.message}`, 'error');
        } finally {
            setLoadingState(loadGithubFile, false);
        }
    }

    // Utility functions
    function enableElement(element) {
        element.disabled = false;
    }

    function disableElement(element) {
        element.disabled = true;
    }

    function resetDownstreamElements(elements) {
        if (elements.includes('repo')) {
            githubRepo.innerHTML = '<option value="">Primero selecciona un usuario</option>';
            disableElement(githubRepo);
        }
        if (elements.includes('path')) {
            githubPath.value = '';
            disableElement(githubPath);
        }
        if (elements.includes('files')) {
            githubFiles.innerHTML = '<option value="">Selecciona repositorio y ruta primero</option>';
            disableElement(githubFiles);
            disableElement(previewGithubFile);
            disableElement(loadGithubFile);
            githubPreview.style.display = 'none';
        }
    }

    function resetAllDownstream() {
        resetDownstreamElements(['repo', 'path', 'files']);
    }

    function setLoadingState(button, isLoading) {
        if (isLoading) {
            button.classList.add('loading-github');
            button.disabled = true;
            const originalText = button.textContent;
            button.dataset.originalText = originalText;
            
            // Agregar texto de carga específico para cada botón
            if (button.id === 'loadRepos') {
                button.textContent = '🔄 Cargando repositorios...';
            } else if (button.id === 'loadFiles') {
                button.textContent = '🔍 Buscando archivos...';
            } else if (button.id === 'previewGithubFile') {
                button.textContent = '👁️ Cargando vista previa...';
            } else if (button.id === 'loadGithubFile') {
                button.textContent = '⬇️ Descargando...';
            }
        } else {
            button.classList.remove('loading-github');
            button.disabled = false;
            
            // Restaurar texto original
            if (button.dataset.originalText) {
                button.textContent = button.dataset.originalText;
                delete button.dataset.originalText;
            }
        }
    }

    function updateRateLimit(response) {
        rateLimitRemaining = parseInt(response.headers.get('X-RateLimit-Remaining') || '60');
        rateLimitReset = parseInt(response.headers.get('X-RateLimit-Reset') || '0');
        
        if (rateLimitRemaining < 10) {
            const resetTime = new Date(rateLimitReset * 1000);
            showNotification(`⚠️ Límite de API próximo a agotarse. Se reinicia: ${resetTime.toLocaleTimeString()}`, 'warning');
        }
    }

    function getErrorMessage(status) {
        switch (status) {
            case 404:
                return 'Usuario, repositorio o archivo no encontrado';
            case 403:
                return 'Límite de API de GitHub alcanzado. Intenta más tarde';
            case 401:
                return 'No autorizado. El repositorio podría ser privado';
            default:
                return 'Error de conexión con GitHub';
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        const backgroundColor = {
            'success': 'linear-gradient(135deg, #2da44e, #298e46)',
            'error': 'linear-gradient(135deg, #d1242f, #cf222e)',
            'warning': 'linear-gradient(135deg, #fb8500, #d97706)',
            'info': 'linear-gradient(135deg, #0969da, #0969da)'
        };
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 16px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            box-shadow: 0 8px 24px rgba(27, 31, 36, 0.15);
            background: ${backgroundColor[type] || backgroundColor.info};
            max-width: 400px;
            word-wrap: break-word;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 14px;
            border: 1px solid rgba(27, 31, 36, 0.15);
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
});
