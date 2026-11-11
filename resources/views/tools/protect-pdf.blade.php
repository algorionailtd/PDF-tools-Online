<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>Password Protect PDF - PDFTools</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    brand: '#e5322d',
                    brandDark: '#c9211c'
                }
            }
        }
    }
    </script>

    <style>
    * {
        box-sizing: border-box
    }

    body {
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif
    }
        html {
        scrollbar-width: none
    }


    .grid-bg {
        background-image: linear-gradient(rgba(229, 50, 45, .055) 1px, transparent 1px), linear-gradient(90deg, rgba(229, 50, 45, .055) 1px, transparent 1px);
        background-size: 28px 28px
    }

    .loader {
        width: 20px;
        height: 20px;
        border: 3px solid #ffffff55;
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }
    </style>
</head>

<body class="bg-white text-slate-900">

    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
            <a href="{{route('home')}}" class="flex items-center gap-2">
                <span
                    class="w-9 h-9 rounded-xl bg-brand text-white flex items-center justify-center font-black">PDF</span>
                <div>
                    <div class="font-black text-lg leading-none">Tools</div>
                    <div class="text-[10px] text-slate-400 font-semibold">Every PDF tool</div>
                </div>
            </a>
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
                <a href="{{route('home')}}" class="hover:text-brand">Home</a>
                <a href="{{route('home')}}#tools" class="hover:text-brand">All Tools</a>
                <a href="{{route('home')}}#workflows" class="hover:text-brand">Workflows</a>
                <a href="{{route('home')}}#about" class="hover:text-brand">About</a>
            </nav>
            <a href="{{route('home')}}#tools"
                class="hidden sm:inline-flex bg-brand hover:bg-brandDark text-white px-5 py-2.5 rounded-xl text-sm font-bold">Explore
                Tools</a>
        </div>
    </header>

    <section class="grid-bg py-14 sm:py-20 border-b border-red-50">
        <div class="max-w-4xl mx-auto px-5 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 text-brand text-sm font-bold">🔐
                PDF Tool 12</div>
            <h1 class="mt-5 text-4xl sm:text-5xl font-black tracking-tight">Password <span class="text-brand">Protect
                    PDF</span></h1>
            <p class="mt-4 text-slate-500 max-w-2xl mx-auto text-base sm:text-lg">Protect your PDF document with a
                password and keep your file secure.</p>
        </div>
    </section>

    <main class="max-w-4xl mx-auto px-5 py-10">

        <div
            class="mb-6 rounded-2xl bg-blue-50 border border-blue-100 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="font-black text-blue-900">Free Uploads</div>
                <div class="text-sm text-blue-700 mt-1">You have 4 free file uploads available across all PDF tools.
                </div>
            </div>
            <div class="text-2xl font-black text-blue-900"><span id="usageCount">0</span> / 4</div>
        </div>

        <div id="uploadCard" class="border-2 border-dashed border-slate-300 hover:bg-red-50 hover:border-red-300 rounded-3xl p-8 sm:p-12 text-center bg-white cursor-pointer">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-red-50 text-brand flex items-center justify-center text-3xl">🔐
            </div>
            <h2 class="mt-5 text-2xl font-black">Upload your PDF</h2>
            <p class="mt-2 text-slate-500">Drag & drop your PDF here or choose a file.</p>
            <p class="mt-1 text-xs text-slate-400">PDF • No frontend MB upload limit</p>
            <input id="input" type="file" accept="application/pdf" class="hidden">
            <button type="button" onclick="event.stopPropagation();$('input').click()"
                class="mt-6 bg-brand hover:bg-brandDark text-white px-7 py-3 rounded-xl font-bold">Choose PDF</button>
        </div>

        <div id="fileCard" class="hidden mt-6 rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-red-50 flex items-center justify-center text-2xl">🔐
                    </div>
                    <div class="min-w-0">
                        <div id="fileName" class="font-bold truncate"></div>
                        <div id="fileSize" class="text-sm text-slate-400"></div>
                    </div>
                </div>
                <button onclick="resetAll()" class="text-sm font-bold text-red-600">Delete</button>
            </div>
        </div>

        <div id="optionsCard" class="hidden mt-6 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-11 h-11 rounded-xl bg-red-50 text-brand flex items-center justify-center text-xl">🔒</div>
                <div>
                    <h2 class="font-black text-xl">Set Password</h2>
                    <p class="text-sm text-slate-400">Create a strong password for your PDF.</p>
                </div>
            </div>

            <label class="block text-sm font-bold text-slate-700">Password</label>
            <div class="relative mt-2">
                <input id="password" type="password" placeholder="Example: PdfTools@2026"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 pr-20 outline-none focus:border-brand">
                <button type="button" onclick="togglePassword('password',this)"
                    class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-semibold text-slate-500 hover:text-brand">Show</button>
            </div>

            <label class="block text-sm font-bold text-slate-700 mt-5">Confirm Password</label>
            <div class="relative mt-2">
                <input id="confirmPassword" type="password" placeholder="Confirm password"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 pr-20 outline-none focus:border-brand">
                <button type="button" onclick="togglePassword('confirmPassword',this)"
                    class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-semibold text-slate-500 hover:text-brand">Show</button>
            </div>

            <div class="mt-5 rounded-xl bg-red-50 p-4">
                <p class="text-xs text-red-700 leading-5">🔒 Use 8+ characters with uppercase, lowercase, numbers and
                    special characters.</p>
            </div>

            <div id="error"
                class="hidden mt-5 rounded-xl bg-red-50 border border-red-100 text-red-700 p-4 text-sm font-semibold">
            </div>

            <button id="protect" onclick="protectPDF()"
                class="mt-6 w-full bg-brand hover:bg-brandDark text-white py-4 rounded-xl font-black">Protect PDF
                →</button>

            <div id="progress" class="hidden mt-5">
                <div class="flex justify-between text-sm mb-2"><span id="ptext">Processing...</span><span
                        id="pct">0%</span></div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div id="bar" class="h-full bg-brand rounded-full transition-all" style="width:0%"></div>
                </div>
            </div>
        </div>

        <x-payment-upgrade-card id="paymentCard" />

        <div id="result" class="hidden mt-6 rounded-3xl border border-green-200 bg-white overflow-hidden">
            <div class="bg-green-50 border-b border-green-100 p-5">
                <div class="text-green-700 font-black text-lg">✓ PDF Protected Successfully</div>
                <div id="resultInfo" class="text-sm text-green-600 mt-1">Your password protected PDF is ready.</div>
            </div>
            <div class="p-6 flex flex-col sm:flex-row gap-3">
                <button onclick="viewPDF()"
                    class="flex-1 bg-slate-900 hover:bg-black text-white py-3.5 rounded-xl font-black">👁 View
                    PDF</button>
                <a id="download"
                    class="flex-1 bg-brand hover:bg-brandDark text-white py-3.5 rounded-xl font-black text-center cursor-pointer">⬇
                    Download PDF</a>
            </div>
        </div>

    </main>

    <div id="viewer" class="hidden fixed inset-0 z-50 bg-black/90 p-3 sm:p-6">
        <div class="h-full max-w-6xl mx-auto bg-white rounded-2xl overflow-hidden relative">
            <button onclick="closeViewer()"
                class="absolute right-3 top-3 z-10 w-10 h-10 rounded-full bg-black text-white font-black">×</button>
            <iframe id="frame" class="w-full h-full" title="Protected PDF Preview"></iframe>
        </div>
    </div>

    <footer class="border-t border-slate-100 mt-10">
        <div class="max-w-7xl mx-auto px-5 sm:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <div class="font-black text-lg">PDF<span class="text-brand">Tools</span></div>
                <div class="text-sm text-slate-400 mt-1">Simple, fast and reliable online PDF tools.</div>
            </div>
            <a href="{{route('home')}}#tools" class="text-sm font-bold text-slate-600">All Tools</a>
        </div>
    </footer>

    <script>
    const $ = id => document.getElementById(id);
    let file = null,
        outputUrl = null;

    function fmt(n) {
        if (n < 1024) return n + " B";
        if (n < 1048576) return (n / 1024).toFixed(1) + " KB";
        if (n < 1073741824) return (n / 1048576).toFixed(1) + " MB";
        return (n / 1073741824).toFixed(2) + " GB";
    }

    function showError(m) {
        $("error").textContent = m;
        $("error").classList.remove("hidden");
    }

    function clearError() {
        $("error").classList.add("hidden");
        $("error").textContent = "";
    }

    $("uploadCard").onclick = e => {
        if (!e.target.closest("button")) $("input").click();
    };

    $("input").onchange = function() {
        if (this.files[0]) loadFile(this.files[0]);
    };

    ["dragenter", "dragover"].forEach(t => {
        $("uploadCard").addEventListener(t, e => {
            e.preventDefault();
            $("uploadCard").classList.add("bg-red-50", "border-brand");
        });
    });

    ["dragleave", "drop"].forEach(t => {
        $("uploadCard").addEventListener(t, e => {
            e.preventDefault();
            $("uploadCard").classList.remove("bg-red-50", "border-brand");
        });
    });

    $("uploadCard").ondrop = e => {
        if (e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    };

    function loadFile(f) {
        clearError();
        if (f.size === 0) {
            $("input").value = "";
            return showError('"' + f.name + '" is empty (0 bytes). Please choose a non-empty PDF.');
        }
        if (f.type !== "application/pdf" && !f.name.toLowerCase().endsWith(".pdf"))
            return showError("Please select a valid PDF file.");

        file = f;
        $("fileName").textContent = f.name;
        $("fileSize").textContent = "PDF Document • " + fmt(f.size);
        $("fileCard").classList.remove("hidden");
        $("optionsCard").classList.remove("hidden");
        $("uploadCard").classList.add("hidden");
        $("result").classList.add("hidden");
        $("optionsCard").scrollIntoView({
            behavior: "smooth",
            block: "center"
        });
    }

    function togglePassword(id, btn) {
        const input = $(id);
        input.type = input.type === "password" ? "text" : "password";
        btn.textContent = input.type === "password" ? "Show" : "Hide";
    }

    function progress(v, t) {
        $("bar").style.width = v + "%";
        $("pct").textContent = v + "%";
        $("ptext").textContent = t;
    }

    function updateUsage(used, remaining) {
        used = Math.min(Math.max(Number(used || 0), 0), 4);
        $("usageCount").textContent = used;
    }

    async function reserveUpload() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || "{{csrf_token()}}";
        const response = await fetch("{{route('pdf.usage.reserve')}}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrf
            },
            body: JSON.stringify({
                file_count: 1
            })
        });
        let data = {};
        try {
            data = await response.json()
        } catch (e) {}

        if (data.requires_payment || (data.allowed === false && data.free_uploads_used >= 4)) {
            updateUsage(4, 0);
            $("paymentCard").classList.remove("hidden");
            $("paymentCard").scrollIntoView({
                behavior: "smooth",
                block: "center"
            });
            throw new Error("FREE_UPLOAD_FINISHED");
        }

        if (data.premium) {
            $("usageCount").textContent = "Unlimited";
            return data;
        }

        if (!response.ok || !data.allowed)
            throw new Error(data.message || "Upload could not be reserved.");

        updateUsage(data.free_uploads_used ?? 0, data.remaining);
        return data;
    }

    async function protectPDF() {
        clearError();

        if (!file) return showError("Please select a PDF first.");

        const password = $("password").value.trim();
        const confirmPassword = $("confirmPassword").value.trim();
        const strong = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/;

        if (!strong.test(password))
            return showError("Use 8+ characters with uppercase, lowercase, numbers and a special character.");

        if (password !== confirmPassword)
            return showError("Passwords do not match.");

        const btn = $("protect");
        btn.disabled = true;
        btn.innerHTML =
            '<span class="inline-flex items-center justify-center gap-2"><span class="loader"></span>Protecting...</span>';
        $("progress").classList.remove("hidden");

        try {
            progress(10, "Checking free upload...");
            await reserveUpload();

            progress(25, "Preparing PDF...");
            const form = new FormData();
            form.append("pdf", file);
            form.append("password", password);

            progress(40, "Applying password protection...");
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const response = await fetch("{{route('protect.pdf.process')}}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrf,
                    "Accept": "application/pdf"
                },
                body: form
            });

            if (!response.ok) {
                let msg = "Server could not protect PDF.";
                try {
                    const d = await response.json();
                    if (d.message) msg = d.message
                } catch (e) {}
                throw new Error(msg);
            }

            progress(75, "Creating protected PDF...");
            const blob = await response.blob();

            if (!blob || !blob.size)
                throw new Error("The server returned an empty PDF.");

            if (outputUrl) URL.revokeObjectURL(outputUrl);
            outputUrl = URL.createObjectURL(blob);

            $("download").href = outputUrl;
            $("download").download = "protected-" + file.name;
            $("resultInfo").textContent = "Password protection applied • " + fmt(blob.size);

            progress(100, "Protected PDF ready.");
            $("result").classList.remove("hidden");
            $("result").scrollIntoView({
                behavior: "smooth",
                block: "center"
            });

        } catch (e) {
            console.error(e);
            if (e.message !== "FREE_UPLOAD_FINISHED") showError(e.message || "Could not protect this PDF.");
            $("progress").classList.add("hidden");
        } finally {
            btn.disabled = false;
            btn.textContent = "Protect PDF →";
        }
    }

    function viewPDF() {
        if (!outputUrl) return;
        $("frame").src = outputUrl;
        $("viewer").classList.remove("hidden");
        document.body.classList.add("overflow-hidden");
    }

    function closeViewer() {
        $("viewer").classList.add("hidden");
        $("frame").src = "";
        document.body.classList.remove("overflow-hidden");
    }

    function resetAll() {
        closeViewer();
        if (outputUrl) {
            URL.revokeObjectURL(outputUrl);
            outputUrl = null
        }
        file = null;
        $("input").value = "";
        $("password").value = "";
        $("confirmPassword").value = "";
        $("uploadCard").classList.remove("hidden");
        $("fileCard").classList.add("hidden");
        $("optionsCard").classList.add("hidden");
        $("result").classList.add("hidden");
        $("paymentCard").classList.add("hidden");
        $("progress").classList.add("hidden");
        progress(0, "Processing...");
        clearError();
    }

    updateUsage(0, 4);
    </script>

</body>

</html>