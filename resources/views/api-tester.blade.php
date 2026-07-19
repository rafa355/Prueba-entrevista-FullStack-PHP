<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Tester - Customer Management</title>
    <style>
        :root {
            --bg: #0f0f0f;
            --surface: #1a1a1a;
            --surface-2: #242424;
            --border: #333;
            --text: #e0e0e0;
            --text-muted: #888;
            --accent: #6366f1;
            --accent-hover: #818cf8;
            --green: #22c55e;
            --red: #ef4444;
            --yellow: #eab308;
            --blue: #3b82f6;
            --radius: 8px;
            --font-mono: 'SF Mono', 'Fira Code', 'Consolas', monospace;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 24px;
        }

        header h1 {
            font-size: 20px;
            font-weight: 600;
            color: var(--text);
        }

        header .badge {
            background: var(--accent);
            color: white;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
        }

        .layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 24px;
            min-height: calc(100vh - 140px);
        }

        .sidebar {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 16px;
        }

        .sidebar h2 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 12px;
            font-weight: 600;
        }

        .endpoint-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .endpoint-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .endpoint-item:hover {
            border-color: var(--accent);
            background: rgba(99, 102, 241, 0.1);
        }

        .endpoint-item.active {
            border-color: var(--accent);
            background: rgba(99, 102, 241, 0.15);
        }

        .method-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 6px;
            border-radius: 4px;
            font-family: var(--font-mono);
            min-width: 50px;
            text-align: center;
        }

        .method-post { background: var(--green); color: #000; }
        .method-get { background: var(--blue); color: #fff; }
        .method-delete { background: var(--red); color: #fff; }

        .endpoint-path {
            font-size: 13px;
            font-family: var(--font-mono);
            color: var(--text);
        }

        .main-panel {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .panel {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            background: var(--surface-2);
        }

        .panel-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .panel-body {
            padding: 16px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 10px 12px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            font-size: 13px;
            font-family: var(--font-mono);
            transition: border-color 0.15s ease;
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--accent);
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
            line-height: 1.5;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .response-section {
            flex: 1;
        }

        .response-meta {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-badge {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 4px;
            font-family: var(--font-mono);
        }

        .status-2xx { background: rgba(34, 197, 94, 0.2); color: var(--green); }
        .status-4xx { background: rgba(239, 68, 68, 0.2); color: var(--red); }
        .status-5xx { background: rgba(234, 179, 8, 0.2); color: var(--yellow); }

        .response-time {
            font-size: 12px;
            color: var(--text-muted);
            font-family: var(--font-mono);
        }

        .response-body {
            background: var(--bg);
            border-radius: 6px;
            padding: 16px;
            font-family: var(--font-mono);
            font-size: 13px;
            line-height: 1.6;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-word;
            min-height: 150px;
            max-height: 400px;
            overflow-y: auto;
        }

        .json-key { color: #93c5fd; }
        .json-string { color: #86efac; }
        .json-number { color: #fcd34d; }
        .json-boolean { color: #c4b5fd; }
        .json-null { color: #9ca3af; }

        .token-section {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 16px;
            margin-bottom: 20px;
        }

        .token-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .token-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .token-value {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 10px 12px;
        }

        .token-input {
            flex: 1;
            background: transparent;
            border: none;
            color: var(--text);
            font-family: var(--font-mono);
            font-size: 12px;
            outline: none;
        }

        .token-copy {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 4px 8px;
            color: var(--text-muted);
            font-size: 11px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .token-copy:hover {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 200px;
            color: var(--text-muted);
            text-align: center;
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 14px;
        }

        .loading-spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .hidden { display: none !important; }

        @media (max-width: 900px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>API Tester</h1>
            <span class="badge">Customer Management</span>
        </header>

        <div class="layout">
            <aside class="sidebar">
                <h2>Endpoints</h2>
                <div class="endpoint-list">
                    <div class="endpoint-item active" data-endpoint="login">
                        <span class="method-badge method-post">POST</span>
                        <span class="endpoint-path">/api/login</span>
                    </div>
                    <div class="endpoint-item" data-endpoint="store">
                        <span class="method-badge method-post">POST</span>
                        <span class="endpoint-path">/api/customers</span>
                    </div>
                    <div class="endpoint-item" data-endpoint="show">
                        <span class="method-badge method-get">GET</span>
                        <span class="endpoint-path">/api/customers</span>
                    </div>
                    <div class="endpoint-item" data-endpoint="delete">
                        <span class="method-badge method-delete">DELETE</span>
                        <span class="endpoint-path">/api/customers/{dni}</span>
                    </div>
                    <div class="endpoint-item" data-endpoint="regions">
                        <span class="method-badge method-get">GET</span>
                        <span class="endpoint-path">/api/regions</span>
                    </div>
                    <div class="endpoint-item" data-endpoint="customersAll">
                        <span class="method-badge method-get">GET</span>
                        <span class="endpoint-path">/api/customers/all</span>
                    </div>
                </div>
            </aside>

            <main class="main-panel">
                <div class="token-section">
                    <div class="token-header">
                        <span class="token-label">Authorization Token</span>
                    </div>
                    <div class="token-value">
                        <input type="text" class="token-input" id="tokenInput" placeholder="Haz login para obtener un token..." readonly>
                        <button class="token-copy" onclick="copyToken()">Copiar</button>
                    </div>
                </div>

                <div class="panel" id="requestPanel">
                    <div class="panel-header">
                        <span class="panel-title" id="requestTitle">Request</span>
                        <button class="btn btn-primary" id="sendBtn" onclick="sendRequest()">
                            <span id="sendBtnText">Enviar</span>
                        </button>
                    </div>
                    <div class="panel-body">
                        <div id="formLogin">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-input" id="loginEmail" value="cliente@test.com">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Password</label>
                                <input type="text" class="form-input" id="loginPassword" value="123456">
                            </div>
                        </div>

                        <div id="formStore" class="hidden">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">DNI</label>
                                    <input type="text" class="form-input" id="storeDni" placeholder="12345678">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-input" id="storeEmail" placeholder="cliente@test.com">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Password</label>
                                    <input type="text" class="form-input" id="storePassword" placeholder="123456">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-input" id="storeName" placeholder="Juan">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-input" id="storeLastName" placeholder="Pérez">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-input" id="storeAddress" placeholder="Av. Principal 123">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Region ID</label>
                                    <input type="number" class="form-input" id="storeIdReg" value="1">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Commune ID</label>
                                    <input type="number" class="form-input" id="storeIdCom" value="1">
                                </div>
                            </div>
                        </div>

                        <div id="formShow" class="hidden">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">DNI</label>
                                    <input type="text" class="form-input" id="showDni" placeholder="12345678">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-input" id="showEmail" placeholder="cliente@test.com">
                                </div>
                            </div>
                        </div>

                        <div id="formDelete" class="hidden">
                            <div class="form-group">
                                <label class="form-label">DNI</label>
                                <input type="text" class="form-input" id="deleteDni" placeholder="12345678">
                            </div>
                        </div>

                        <div id="formRegions" class="hidden">
                            <div class="empty-state" style="min-height: 80px;">
                                <p style="font-size: 13px; color: var(--text-muted);">No parameters required. Click Send to fetch regions.</p>
                            </div>
                        </div>

                        <div id="formCustomersAll" class="hidden">
                            <div class="empty-state" style="min-height: 80px;">
                                <p style="font-size: 13px; color: var(--text-muted);">No parameters required. Click Send to fetch all customers.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel response-section">
                    <div class="panel-header">
                        <span class="panel-title">Response</span>
                        <div class="response-meta" id="responseMeta"></div>
                    </div>
                    <div class="panel-body">
                        <div class="response-body" id="responseBody">
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <p>Selecciona un endpoint y haz clic en Enviar</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const API_BASE = '{{ url("/api") }}';
        let currentEndpoint = 'login';
        let authToken = '';

        document.querySelectorAll('.endpoint-item').forEach(item => {
            item.addEventListener('click', () => {
                document.querySelectorAll('.endpoint-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                currentEndpoint = item.dataset.endpoint;
                updateFormVisibility();
            });
        });

        function updateFormVisibility() {
            document.getElementById('formLogin').classList.toggle('hidden', currentEndpoint !== 'login');
            document.getElementById('formStore').classList.toggle('hidden', currentEndpoint !== 'store');
            document.getElementById('formShow').classList.toggle('hidden', currentEndpoint !== 'show');
            document.getElementById('formDelete').classList.toggle('hidden', currentEndpoint !== 'delete');
            document.getElementById('formRegions').classList.toggle('hidden', currentEndpoint !== 'regions');
            document.getElementById('formCustomersAll').classList.toggle('hidden', currentEndpoint !== 'customersAll');

            const titles = {
                login: 'POST /api/login',
                store: 'POST /api/customers',
                show: 'GET /api/customers',
                delete: 'DELETE /api/customers/{dni}',
                regions: 'GET /api/regions',
                customersAll: 'GET /api/customers/all'
            };
            document.getElementById('requestTitle').textContent = titles[currentEndpoint];
        }

        async function sendRequest() {
            const btn = document.getElementById('sendBtn');
            const btnText = document.getElementById('sendBtnText');
            const responseBody = document.getElementById('responseBody');
            const responseMeta = document.getElementById('responseMeta');

            btn.disabled = true;
            btnText.innerHTML = '<span class="loading-spinner"></span> Enviando...';

            const startTime = performance.now();

            try {
                let response;
                switch (currentEndpoint) {
                    case 'login':
                        response = await fetch(`${API_BASE}/login`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                email: document.getElementById('loginEmail').value,
                                password: document.getElementById('loginPassword').value
                            })
                        });
                        break;

                    case 'store':
                        response = await fetch(`${API_BASE}/customers`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${authToken}`
                            },
                            body: JSON.stringify({
                                dni: document.getElementById('storeDni').value,
                                id_reg: parseInt(document.getElementById('storeIdReg').value),
                                id_com: parseInt(document.getElementById('storeIdCom').value),
                                email: document.getElementById('storeEmail').value,
                                password: document.getElementById('storePassword').value,
                                name: document.getElementById('storeName').value,
                                last_name: document.getElementById('storeLastName').value,
                                address: document.getElementById('storeAddress').value || null
                            })
                        });
                        break;

                    case 'show':
                        const params = new URLSearchParams();
                        const dni = document.getElementById('showDni').value;
                        const email = document.getElementById('showEmail').value;
                        if (dni) params.append('dni', dni);
                        if (email) params.append('email', email);
                        response = await fetch(`${API_BASE}/customers?${params}`, {
                            headers: { 'Authorization': `Bearer ${authToken}` }
                        });
                        break;

                    case 'delete':
                        const delDni = document.getElementById('deleteDni').value;
                        response = await fetch(`${API_BASE}/customers/${delDni}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${authToken}` }
                        });
                        break;

                    case 'regions':
                        response = await fetch(`${API_BASE}/regions`, {
                            headers: { 'Authorization': `Bearer ${authToken}` }
                        });
                        break;

                    case 'customersAll':
                        response = await fetch(`${API_BASE}/customers/all`, {
                            headers: { 'Authorization': `Bearer ${authToken}` }
                        });
                        break;
                }

                const duration = Math.round(performance.now() - startTime);
                const data = await response.json();

                const statusClass = response.status < 300 ? 'status-2xx' : response.status < 500 ? 'status-4xx' : 'status-5xx';
                responseMeta.innerHTML = `
                    <span class="status-badge ${statusClass}">${response.status}</span>
                    <span class="response-time">${duration}ms</span>
                `;

                if (currentEndpoint === 'login && data.success && data.data?.token') {
                    // handled below
                }

                if (currentEndpoint === 'login' && data.success && data.data?.token) {
                    authToken = data.data.token;
                    document.getElementById('tokenInput').value = authToken;
                }

                responseBody.innerHTML = syntaxHighlight(JSON.stringify(data, null, 2));
            } catch (error) {
                const duration = Math.round(performance.now() - startTime);
                responseMeta.innerHTML = `<span class="status-badge status-5xx">Error</span>`;
                responseBody.innerHTML = `<span style="color: var(--red);">${error.message}</span>`;
            } finally {
                btn.disabled = false;
                btnText.textContent = 'Enviar';
            }
        }

        function syntaxHighlight(json) {
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+-]?\d+)?)/g, function (match) {
                let cls = 'json-number';
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = 'json-key';
                    } else {
                        cls = 'json-string';
                    }
                } else if (/true|false/.test(match)) {
                    cls = 'json-boolean';
                } else if (/null/.test(match)) {
                    cls = 'json-null';
                }
                return '<span class="' + cls + '">' + match + '</span>';
            });
        }

        function copyToken() {
            const input = document.getElementById('tokenInput');
            if (input.value) {
                navigator.clipboard.writeText(input.value);
                const btn = document.querySelector('.token-copy');
                btn.textContent = 'Copiado!';
                setTimeout(() => btn.textContent = 'Copiar', 1500);
            }
        }

        updateFormVisibility();
    </script>
</body>
</html>
