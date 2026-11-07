<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnix ERP • Cliente API REST (Estilo Postman)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        pre { font-family: 'Consolas', 'Courier New', monospace; }
    </style>
</head>
<body class="bg-[#121212] text-[#E0E0E0] min-h-screen flex flex-col font-sans">

    <!-- Topbar -->
    <header class="bg-[#1E1E1E] border-b border-[#2E2E2E] px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex gap-1.5 mr-2">
                <span class="w-3 h-3 rounded-full bg-[#FF5F56]"></span>
                <span class="w-3 h-3 rounded-full bg-[#FFBD2E]"></span>
                <span class="w-3 h-3 rounded-full bg-[#27C93F]"></span>
            </div>
            <i class="fa-solid fa-bolt text-amber-400 text-lg"></i>
            <h1 class="text-sm font-semibold tracking-wide text-white">Turnix ERP • Cliente Interactivo de Pruebas API REST (Clase 13)</h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#0D5329] text-[#4ADE80] text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-[#4ADE80] animate-pulse"></span> Servidor Local Activo :8000
            </span>
            <a href="/dashboard" class="text-xs text-gray-400 hover:text-white transition">Ir al ERP &rarr;</a>
        </div>
    </header>

    <div class="flex-1 flex flex-col lg:flex-row p-6 gap-6 max-w-7xl mx-auto w-full">

        <!-- Sidebar con Botones de Prueba Rápida -->
        <aside class="w-full lg:w-80 bg-[#1E1E1E] rounded-xl border border-[#2E2E2E] p-4 flex flex-col gap-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 px-1">Pruebas Rápidas con 1 Clic</h2>
            <p class="text-xs text-gray-400 px-1">Haz clic en cualquiera para cargar y ejecutar la petición al instante:</p>

            <div class="flex flex-col gap-2 mt-2">
                <button onclick="runTest('login')" class="w-full text-left p-3 rounded-lg bg-[#262626] hover:bg-[#333333] border border-emerald-500/30 transition flex flex-col gap-1 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-orange-400">POST /api/v1/login</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-900/60 text-emerald-400">201 Created</span>
                    </div>
                    <span class="text-xs text-gray-300 font-medium">1. Login Vendedor (Obtener Token)</span>
                </button>

                <button onclick="runTest('products')" class="w-full text-left p-3 rounded-lg bg-[#262626] hover:bg-[#333333] border border-blue-500/30 transition flex flex-col gap-1 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-400">GET /api/v1/products</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-900/60 text-blue-400">200 OK</span>
                    </div>
                    <span class="text-xs text-gray-300 font-medium">2. Listar Productos (Con Token)</span>
                </button>

                <button onclick="runTest('me')" class="w-full text-left p-3 rounded-lg bg-[#262626] hover:bg-[#333333] border border-purple-500/30 transition flex flex-col gap-1 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-400">GET /api/v1/me</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-purple-900/60 text-purple-400">200 OK</span>
                    </div>
                    <span class="text-xs text-gray-300 font-medium">3. Consultar Perfil y Rol Activo</span>
                </button>

                <button onclick="runTest('unauthenticated')" class="w-full text-left p-3 rounded-lg bg-[#262626] hover:bg-[#333333] border border-red-500/30 transition flex flex-col gap-1 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-400">GET /api/v1/products</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-red-900/60 text-red-400">401 Unauth</span>
                    </div>
                    <span class="text-xs text-gray-300 font-medium">4. Petición Sin Token (Rechazo)</span>
                </button>

                <button onclick="runTest('forbidden')" class="w-full text-left p-3 rounded-lg bg-[#262626] hover:bg-[#333333] border border-amber-500/30 transition flex flex-col gap-1 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-orange-400">POST /api/v1/sales</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-900/60 text-amber-400">403 Forbidden</span>
                    </div>
                    <span class="text-xs text-gray-300 font-medium">5. Almacenista intenta Vender</span>
                </button>

                <button onclick="runTest('out_of_stock')" class="w-full text-left p-3 rounded-lg bg-[#262626] hover:bg-[#333333] border border-pink-500/30 transition flex flex-col gap-1 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-orange-400">POST /api/v1/sales</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-pink-900/60 text-pink-400">422 Unprocess</span>
                    </div>
                    <span class="text-xs text-gray-300 font-medium">6. Venta con Stock Insuficiente</span>
                </button>

                <button onclick="runTest('sale_success')" class="w-full text-left p-3 rounded-lg bg-[#262626] hover:bg-[#333333] border border-indigo-500/30 transition flex flex-col gap-1 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-orange-400">POST /api/v1/sales</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-indigo-900/60 text-indigo-400">201 Created</span>
                    </div>
                    <span class="text-xs text-gray-300 font-medium">7. Registrar Venta Exitosa (Kárdex)</span>
                </button>
            </div>

            <!-- Token Guardado -->
            <div class="mt-auto pt-3 border-t border-[#2E2E2E]">
                <span class="text-[11px] font-semibold text-gray-400">Token Activo en Memoria:</span>
                <p id="token_preview" class="text-[10px] font-mono text-emerald-400 truncate mt-1 bg-[#121212] p-1.5 rounded">Ninguno aún (haz login primero)</p>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="flex-1 flex flex-col gap-4">

            <!-- Request Bar -->
            <div class="bg-[#1E1E1E] rounded-xl border border-[#2E2E2E] p-4 flex flex-col gap-4">
                <div class="flex gap-2">
                    <select id="req_method" class="bg-[#2D2D2D] border border-[#3E3E3E] text-sm font-bold text-orange-400 rounded-lg px-3 py-2 outline-none">
                        <option value="POST">POST</option>
                        <option value="GET">GET</option>
                    </select>
                    <input id="req_url" type="text" value="http://127.0.0.1:8000/api/v1/login" class="flex-1 bg-[#121212] border border-[#3E3E3E] text-sm font-mono text-gray-200 px-3 py-2 rounded-lg outline-none focus:border-blue-500">
                    <button onclick="sendManualRequest()" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 py-2 rounded-lg text-sm flex items-center gap-2 transition">
                        <i class="fa-solid fa-paper-plane text-xs"></i> Send
                    </button>
                </div>

                <!-- Tabs Request Body / Headers -->
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-center text-xs text-gray-400">
                        <span class="font-semibold">Request Payload (JSON)</span>
                        <span id="req_auth_indicator" class="text-amber-400">Sin autenticación</span>
                    </div>
                    <textarea id="req_body" rows="5" class="w-full bg-[#121212] border border-[#2E2E2E] rounded-lg p-3 text-xs font-mono text-gray-300 outline-none focus:border-gray-600 resize-none">{
  "email": "vendedor@test.com",
  "password": "password",
  "device_name": "turnix-web-client"
}</textarea>
                </div>
            </div>

            <!-- Response Window -->
            <div class="flex-1 bg-[#1E1E1E] rounded-xl border border-[#2E2E2E] p-4 flex flex-col gap-3 min-h-[350px]">
                <div class="flex items-center justify-between pb-3 border-b border-[#2E2E2E]">
                    <span class="text-xs font-bold text-gray-300 uppercase tracking-wider">Response Body</span>
                    <div class="flex items-center gap-3">
                        <span id="res_time" class="text-xs text-gray-400">Tiempo: - ms</span>
                        <span id="res_status_badge" class="px-2.5 py-0.5 rounded text-xs font-bold bg-[#2A2A2A] text-gray-400">Estado: En espera</span>
                    </div>
                </div>

                <pre id="res_body" class="flex-1 overflow-auto bg-[#121212] p-4 rounded-lg text-xs text-gray-300 border border-[#2E2E2E]">{
  "instrucciones": "Haz clic en '1. Login Vendedor' en la barra lateral para ver el token en vivo, o pulsa Send."
}</pre>
            </div>
        </main>
    </div>

    <script>
        let currentToken = '';

        const scenarios = {
            login: {
                method: 'POST',
                url: 'http://127.0.0.1:8000/api/v1/login',
                body: JSON.stringify({
                    email: "vendedor@test.com",
                    password: "password",
                    device_name: "turnix-web-client"
                }, null, 2),
                auth: false
            },
            products: {
                method: 'GET',
                url: 'http://127.0.0.1:8000/api/v1/products',
                body: '',
                auth: true
            },
            me: {
                method: 'GET',
                url: 'http://127.0.0.1:8000/api/v1/me',
                body: '',
                auth: true
            },
            unauthenticated: {
                method: 'GET',
                url: 'http://127.0.0.1:8000/api/v1/products',
                body: '',
                auth: false,
                forceNoAuth: true
            },
            forbidden: {
                method: 'POST',
                url: 'http://127.0.0.1:8000/api/v1/sales',
                body: JSON.stringify({
                    client_id: 1,
                    sale_date: new Date().toISOString().slice(0,10),
                    items: [{ product_id: 1, quantity: 1 }]
                }, null, 2),
                customAuthLogin: { email: 'almacenista@test.com', password: 'password', role: 'almacenista' }
            },
            out_of_stock: {
                method: 'POST',
                url: 'http://127.0.0.1:8000/api/v1/sales',
                body: JSON.stringify({
                    client_id: 1,
                    sale_date: new Date().toISOString().slice(0,10),
                    items: [{ product_id: 1, quantity: 99999 }]
                }, null, 2),
                auth: true
            },
            sale_success: {
                method: 'POST',
                url: 'http://127.0.0.1:8000/api/v1/sales',
                body: JSON.stringify({
                    client_id: 1,
                    sale_date: new Date().toISOString().slice(0,10),
                    notes: "Venta registrada desde el cliente interactivo API",
                    items: [{ product_id: 1, quantity: 1 }]
                }, null, 2),
                auth: true
            }
        };

        async function runTest(type) {
            const sc = scenarios[type];
            document.getElementById('req_method').value = sc.method;
            document.getElementById('req_url').value = sc.url;
            document.getElementById('req_body').value = sc.body;

            if (sc.forceNoAuth) {
                document.getElementById('req_auth_indicator').textContent = 'Sin cabecera Authorization (Simulando anónimo)';
                document.getElementById('req_auth_indicator').className = 'text-red-400';
                await executeFetch(sc.method, sc.url, sc.body, null);
                return;
            }

            if (sc.customAuthLogin) {
                // Obtener token para almacenista
                const logRes = await fetch('http://127.0.0.1:8000/api/v1/login', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: sc.customAuthLogin.email, password: sc.customAuthLogin.password, device_name: 'almacen-test', role: sc.customAuthLogin.role })
                });
                const logData = await logRes.json();
                document.getElementById('req_auth_indicator').textContent = `Bearer Token de almacenista (rol: ${logData.rol_activo})`;
                document.getElementById('req_auth_indicator').className = 'text-amber-400';
                await executeFetch(sc.method, sc.url, sc.body, logData.token);
                return;
            }

            if (sc.auth) {
                if (!currentToken) {
                    // Si no tiene token, hacer login automático de vendedor primero
                    const logRes = await fetch('http://127.0.0.1:8000/api/v1/login', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: 'vendedor@test.com', password: 'password', device_name: 'auto-login' })
                    });
                    const logData = await logRes.json();
                    currentToken = logData.token;
                    document.getElementById('token_preview').textContent = currentToken;
                }
                document.getElementById('req_auth_indicator').textContent = 'Bearer Token (vendedor)';
                document.getElementById('req_auth_indicator').className = 'text-emerald-400';
                await executeFetch(sc.method, sc.url, sc.body, currentToken);
            } else {
                document.getElementById('req_auth_indicator').textContent = 'Sin autenticación previa';
                document.getElementById('req_auth_indicator').className = 'text-gray-400';
                await executeFetch(sc.method, sc.url, sc.body, null);
            }
        }

        async function sendManualRequest() {
            const method = document.getElementById('req_method').value;
            const url = document.getElementById('req_url').value;
            const body = document.getElementById('req_body').value;
            await executeFetch(method, url, body, currentToken || null);
        }

        async function executeFetch(method, url, bodyText, token) {
            const resStatusBadge = document.getElementById('res_status_badge');
            const resTime = document.getElementById('res_time');
            const resBody = document.getElementById('res_body');

            resStatusBadge.textContent = 'Enviando...';
            resStatusBadge.className = 'px-2.5 py-0.5 rounded text-xs font-bold bg-gray-700 text-gray-300';
            resBody.textContent = 'Cargando respuesta del servidor...';

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };

            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }

            const startTime = performance.now();
            try {
                const options = { method, headers };
                if (method !== 'GET' && bodyText.trim() !== '') {
                    options.body = bodyText;
                }

                const response = await fetch(url, options);
                const duration = Math.round(performance.now() - startTime);
                resTime.textContent = `Tiempo: ${duration} ms`;

                let data;
                try {
                    data = await response.json();
                } catch {
                    data = await response.text();
                }

                // Guardar token si fue login
                if (url.includes('/login') && data.token) {
                    currentToken = data.token;
                    document.getElementById('token_preview').textContent = currentToken;
                }

                // Colorear badge
                if (response.status >= 200 && response.status < 300) {
                    resStatusBadge.className = 'px-2.5 py-0.5 rounded text-xs font-bold bg-[#0D5329] text-[#4ADE80]';
                } else if (response.status === 401 || response.status === 403) {
                    resStatusBadge.className = 'px-2.5 py-0.5 rounded text-xs font-bold bg-[#681F1F] text-[#FCA5A5]';
                } else if (response.status === 422) {
                    resStatusBadge.className = 'px-2.5 py-0.5 rounded text-xs font-bold bg-amber-900 text-amber-300';
                } else {
                    resStatusBadge.className = 'px-2.5 py-0.5 rounded text-xs font-bold bg-gray-700 text-gray-200';
                }

                resStatusBadge.textContent = `Status: ${response.status} ${response.statusText || ''}`;
                resBody.textContent = typeof data === 'object' ? JSON.stringify(data, null, 2) : data;

            } catch (err) {
                resStatusBadge.className = 'px-2.5 py-0.5 rounded text-xs font-bold bg-red-900 text-red-300';
                resStatusBadge.textContent = 'Error de conexión';
                resBody.textContent = `Error al conectar con la API: ${err.message}`;
            }
        }
    </script>
</body>
</html>
