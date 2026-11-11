<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>Merge PDF - PDFTools</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
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

    html {
        scroll-behavior: smooth;
        scrollbar-width: none
    }

    body {
        font-family: Inter, Arial, sans-serif
    }

    .hero {
        background: #fff8f7;
        background-image: linear-gradient(#e5322d08 1px, transparent 1px), linear-gradient(90deg, #e5322d08 1px, transparent 1px);
        background-size: 45px 45px
    }

    .drag {
        background: #fff5f5 !important;
        border-color: #e5322d !important
    }

    .row {
        transition: .2s
    }

    .row:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px #0f172a0d
    }

    .loader {
        width: 18px;
        height: 18px;
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

    button,
    a {
        transition: .2s ease
    }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[72px] flex items-center justify-between gap-4">
            <a href="{{route('home')}}" class="flex items-center gap-2 shrink-0">
                <span class="bg-brand text-white px-2.5 py-1.5 rounded-lg font-black">PDF</span>
                <span class="text-2xl font-black">Tools</span>
            </a>
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold">
                <a href="{{route('home')}}#home" class="hover:text-brand">Home</a>
                <a href="{{route('home')}}#tools" class="text-brand">All Tools</a>
                <a href="{{route('home')}}#workflows" class="hover:text-brand">Workflows</a>
                <a href="{{route('home')}}#about" class="hover:text-brand">About</a>
            </nav>
            <a href="{{route('home')}}#tools"
                class="hidden sm:block bg-brand hover:bg-brandDark text-white px-6 py-3 rounded-lg text-sm font-bold">Explore
                Tools</a>
        </div>
    </header>

    <section class="hero border-b border-red-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12 sm:py-14 text-center">
            <span
                class="inline-flex rounded-full border border-red-100 bg-red-50 px-4 py-1.5 text-sm font-bold text-brand">📑
                PDF Tool 01</span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black mt-5">Merge <span class="text-brand">PDF Files</span>
            </h1>
            <p class="text-sm sm:text-base text-slate-600 mt-4 max-w-2xl mx-auto">Combine multiple PDF and image files
                into one professional PDF document.</p>
        </div>
    </section>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10">

        <div class="mb-6 rounded-2xl bg-blue-50 border border-blue-100 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="font-bold">Free Uploads</h3>
                    <p id="usageText" class="text-sm text-slate-500 mt-1">You have 4 free file uploads available across
                        all PDF tools.</p>
                </div>
                <div class="text-right">
                    <span id="usedCount" class="text-2xl font-black text-brand">0 / 4</span>
                    <p class="text-xs text-slate-400">Global uploads</p>
                </div>
            </div>
        </div>

        <div id="upload" class="bg-white border-2 border-dashed border-red-200 rounded-3xl p-7 sm:p-10 text-center cursor-pointer hover:border-brand hover:bg-red-50 transition">
            <input id="input" type="file" multiple accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"class="hidden">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center text-3xl hover:bg-red-100 hover:border-brand">📑</div>
            <h2 class="text-xl sm:text-2xl font-bold mt-5">Upload your Files</h2>
            <p class="text-sm sm:text-base text-slate-500 mt-2">Drag & drop PDF or image files here.</p>
            <button id="choose" type="button"class="mt-5 bg-brand hover:bg-brandDark text-white font-bold px-6 py-3 rounded-lg shadow-sm">Choose  Files</button>
            <p class="text-xs text-slate-400 mt-4">PDF • JPG • JPEG • PNG • No frontend MB upload limit</p>
        </div>

        <div id="msg" class="hidden mt-5 p-4 rounded-xl text-sm font-semibold"></div>

        <div id="area" class="hidden mt-6 rounded-3xl border border-slate-200 bg-white overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">Selected Files</h2>
                    <p id="count" class="text-sm text-slate-500 mt-1"></p>
                </div>
                <button id="clear" type="button" class="text-brand hover:text-brandDark font-bold text-sm">Clear
                    All</button>
            </div>

            <div class="p-4 sm:p-6">
                <div id="list" class="space-y-3"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6">
                    <button id="add" type="button"
                        class="border border-slate-300 hover:border-brand hover:text-brand rounded-xl py-3 font-bold">+
                        Add Files</button>
                    <button id="merge" type="button"
                        class="bg-brand hover:bg-brandDark text-white rounded-xl py-3 font-bold">Generate PDF</button>
                </div>

                <x-payment-upgrade-card />

                <div id="result" class="hidden mt-6 rounded-2xl border border-green-200 bg-white overflow-hidden">
                    <div class="bg-green-50 border-b border-green-100 p-5">
                        <h3 class="font-bold text-green-800">✓ PDF Created Successfully</h3>
                        <p id="resultText" class="text-sm text-green-700 mt-1">Your merged PDF is ready.</p>
                    </div>
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button id="view" type="button"
                            class="bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl font-bold">👁 View
                            PDF</button>
                        <a id="download"
                            class="bg-brand hover:bg-brandDark text-white py-3 rounded-xl font-bold text-center">⬇
                            Download PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="viewer" class="hidden fixed inset-0 z-[70] bg-black/90 p-2 sm:p-5">
        <div class="w-full h-full max-w-6xl mx-auto bg-white rounded-2xl overflow-hidden flex flex-col">
            <div class="h-14 shrink-0 px-4 border-b border-slate-200 flex items-center justify-between">
                <b class="text-sm sm:text-base">PDF Preview</b>
                <button id="closeViewer" type="button"
                    class="bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg font-bold">✕ Close</button>
            </div>
            <iframe id="frame" class="w-full flex-1 border-0"></iframe>
        </div>
    </div>

    <footer class="bg-white border-t border-slate-100 mt-10">
        <div
            class="max-w-6xl mx-auto px-4 sm:px-6 py-7 flex flex-col sm:flex-row gap-4 justify-between items-center text-center sm:text-left">
            <b class="text-xl">PDF<span class="text-brand">Tools</span></b>
            <p class="text-sm text-slate-500">© {{date('Y')}} PDFTools. All rights reserved.</p>
            <a href="{{route('home')}}#tools" class="text-brand font-semibold">All Tools →</a>
        </div>
    </footer>

    <script>
    const $ = id => document.getElementById(id);
    let files = [],
        generatedURL = null,
        dragIndex = -1;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    async function loadPdfForMerge(file) {
        if (file.size === 0) {
            throw new Error('"' + file.name + '" is empty (0 bytes). Please choose a non-empty PDF.');
        }

        const bytes = new Uint8Array(await file.arrayBuffer());

        try {
            return {
                type: 'copy',
                document: await PDFLib.PDFDocument.load(bytes, {
                    updateMetadata: false
                })
            };
        } catch (pdfLibError) {
            if (typeof pdfjsLib === 'undefined') {
                throw new Error('Could not read "' + file.name + '": PDF.js did not load.');
            }

            try {
                const document = await pdfjsLib.getDocument({
                    data: bytes.slice(),
                    isEvalSupported: false,
                    useSystemFonts: true
                }).promise;

                return {
                    type: 'render',
                    document
                };
            } catch (error) {
                if (error.name === 'PasswordException') {
                    throw new Error('"' + file.name + '" is password-protected. Remove its password before merging.');
                }
                throw new Error('Could not read "' + file.name + '": ' + (error.message || 'Invalid PDF structure.'));
            }
        }
    }

    async function appendRenderedPdf(output, source) {
        for (let pageNumber = 1; pageNumber <= source.numPages; pageNumber++) {
            const sourcePage = await source.getPage(pageNumber);
            const pageSize = sourcePage.getViewport({
                scale: 1
            });
            const viewport = sourcePage.getViewport({
                scale: 1.5
            });
            const canvas = document.createElement('canvas');
            canvas.width = Math.ceil(viewport.width);
            canvas.height = Math.ceil(viewport.height);
            const context = canvas.getContext('2d', {
                alpha: false
            });
            if (!context) throw new Error('Could not render a PDF page.');

            await sourcePage.render({
                canvasContext: context,
                viewport
            }).promise;

            const image = await output.embedJpg(canvas.toDataURL('image/jpeg', 0.92));
            const page = output.addPage([pageSize.width, pageSize.height]);
            page.drawImage(image, {
                x: 0,
                y: 0,
                width: pageSize.width,
                height: pageSize.height
            });
            canvas.width = 1;
            canvas.height = 1;
        }
    }

    $('choose').onclick = e => {
        e.preventDefault();
        e.stopPropagation();
        $('input').click()
    };
    $('add').onclick = () => $('input').click();
    $('upload').onclick = e => {
        if (!e.target.closest('button')) $('input').click()
    };
    $('input').onchange = e => {
        addFiles([...e.target.files]);
        e.target.value = ''
    };

    ['dragenter', 'dragover'].forEach(t => $('upload').addEventListener(t, e => {
        e.preventDefault();
        $('upload').classList.add('drag')
    }));
    ['dragleave', 'drop'].forEach(t => $('upload').addEventListener(t, e => {
        e.preventDefault();
        $('upload').classList.remove('drag')
    }));
    $('upload').ondrop = e => addFiles([...e.dataTransfer.files]);

    const isPDF = f => f.type === 'application/pdf' || /\.pdf$/i.test(f.name);
    const isImage = f => /^(image\/jpeg|image\/png)$/.test(f.type) || /\.(jpg|jpeg|png)$/i.test(f.name);

    function addFiles(items) {
        const supported = items.filter(f => isPDF(f) || isImage(f));
        const emptyFiles = supported.filter(f => f.size === 0);
        files.push(...supported);
        render();

        const errors = [];
        if (emptyFiles.length) {
            errors.push(emptyFiles.map(f => '"' + f.name + '"').join(', ') +
                ' is empty (0 bytes). Replace it before merging.');
        }
        if (supported.length !== items.length) {
            errors.push('Only PDF, JPG, JPEG and PNG files are allowed.');
        }
        if (errors.length) message(errors.join(' '), false);
    }

    function render() {
        if (!files.length) {
            $('area').classList.add('hidden');
            return
        }
        $('area').classList.remove('hidden');
        $('count').textContent = `${files.length} file(s) selected`;
        $('list').innerHTML = files.map((f, i) => `
<div draggable="true" data-index="${i}" class="row border border-slate-200 rounded-xl p-3 flex items-center gap-3">
<div class="text-slate-400 text-xl">☷</div>
<div class="w-12 h-12 rounded-xl ${isPDF(f)?'bg-red-100':'bg-slate-100'} flex items-center justify-center overflow-hidden shrink-0">
${isPDF(f)?'📕':`<img src="${URL.createObjectURL(f)}" class="w-full h-full object-cover" alt="Preview">`}
</div>
<div class="flex-1 min-w-0"><b class="block truncate">${escapeHTML(f.name)}</b><small class="text-slate-500">${formatSize(f.size)} · ${isPDF(f)?'PDF':'Image'}</small></div>
<span class="text-xs text-slate-400">#${i+1}</span>
<button type="button" onclick="removeFile(${i})" class="bg-brand hover:bg-brandDark text-white px-3 py-2 rounded-lg text-sm font-bold">Delete</button>
</div>`).join('');

        document.querySelectorAll('.row').forEach(row => {
            row.ondragstart = () => dragIndex = +row.dataset.index;
            row.ondragover = e => e.preventDefault();
            row.ondrop = () => {
                const target = +row.dataset.index;
                if (dragIndex !== target) {
                    const item = files.splice(dragIndex, 1)[0];
                    files.splice(target, 0, item);
                    render()
                }
            };
        });
    }

    function removeFile(i) {
        files.splice(i, 1);
        render()
    }

    $('clear').onclick = () => {
        files = [];
        if (generatedURL) {
            URL.revokeObjectURL(generatedURL);
            generatedURL = null
        }
        $('result').classList.add('hidden');
        $('paymentBox').classList.add('hidden');
        $('msg').classList.add('hidden');
        render();
    };

    async function reserveFreeUploads(count) {
        const response = await fetch("{{route('pdf.usage.reserve')}}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                file_count: count
            })
        });
        let data = {};
        try {
            data = await response.json()
        } catch (e) {
            data = {
                message: 'Invalid server response.'
            }
        }
        return {
            ok: response.ok,
            status: response.status,
            ...data
        };
    }

    $('merge').onclick = async () => {
        if (files.length < 2) {
            message('Please select at least 2 files.', false);
            return
        }
        const emptyFile = files.find(file => file.size === 0);
        if (emptyFile) {
            message('"' + emptyFile.name + '" is empty (0 bytes). Replace it before merging.', false);
            return;
        }
        $('merge').disabled = true;
        $('merge').innerHTML =
            '<span class="inline-flex items-center gap-2"><span class="loader"></span>Checking files...</span>';
        const pdfSources = new Map();
        try {
            for (const file of files) {
                if (!isPDF(file)) continue;
                const source = await loadPdfForMerge(file);
                pdfSources.set(file, source);
            }

            const access = await reserveFreeUploads(files.length);

            if (access.requires_payment || !access.allowed) {
                $('paymentBox').classList.remove('hidden');
                $('usageText').textContent = 'Your 4 free file uploads across all PDF tools have been used.';
                $('usedCount').textContent = '4 / 4';
                message(access.message || 'Your free uploads are finished. Please subscribe for $2/month.',
                    false);
                $('paymentBox').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return;
            }

            if (access.premium) {
                $('usageText').textContent = 'Premium access active. Unlimited uploads are allowed.';
                $('usedCount').textContent = 'Unlimited';
            }

            $('merge').innerHTML =
                '<span class="inline-flex items-center gap-2"><span class="loader"></span>Generating PDF...</span>';

            const output = await PDFLib.PDFDocument.create();

            for (const file of files) {
                if (isPDF(file)) {
                    const source = pdfSources.get(file);
                    if (source.type === 'copy') {
                        const pages = await output.copyPages(source.document, source.document.getPageIndices());
                        pages.forEach(p => output.addPage(p));
                    } else {
                        await appendRenderedPdf(output, source.document);
                    }
                } else {
                    const image = /\.png$/i.test(file.name) ? await output.embedPng(await file.arrayBuffer()) :
                        await output.embedJpg(await file.arrayBuffer());
                    const page = output.addPage([595, 842]);
                    const scale = Math.min(535 / image.width, 782 / image.height);
                    page.drawImage(image, {
                        x: (595 - image.width * scale) / 2,
                        y: (842 - image.height * scale) / 2,
                        width: image.width * scale,
                        height: image.height * scale
                    });
                }
            }

            const bytes = await output.save({
                useObjectStreams: false,
                addDefaultPage: false
            });
            if (!bytes?.length) throw new Error('Generated PDF is empty.');

            const mergedBlob = new Blob([bytes], {
                type: 'application/pdf'
            });
            if (!mergedBlob.size) throw new Error('The merged PDF is empty (0 bytes).');

            if (generatedURL) URL.revokeObjectURL(generatedURL);
            generatedURL = URL.createObjectURL(mergedBlob);

            $('download').href = generatedURL;
            $('download').download = 'merged-pdf.pdf';
            const hasRasterizedPdf = [...pdfSources.values()].some(source => source.type === 'render');
            $('resultText').textContent = `${files.length} files merged successfully into one PDF.` +
                (hasRasterizedPdf ? ' Protected PDF pages were merged as images.' : '');
            $('result').classList.remove('hidden');

            if (typeof access.free_uploads_used !== 'undefined') {
                const used = Number(access.free_uploads_used),
                    remaining = Math.max(0, 4 - used);
                $('usedCount').textContent = `${Math.min(4,used)} / 4`;
                $('usageText').textContent = remaining === 0 ?
                    'Your 4 free file uploads across all PDF tools have been used.' :
                    `${remaining} free upload${remaining===1?'':'s'} remaining across all PDF tools.`;
            } else if (typeof access.remaining === 'number') {
                const remaining = Math.max(0, Number(access.remaining)),
                    used = 4 - remaining;
                $('usedCount').textContent = `${used} / 4`;
                $('usageText').textContent = remaining === 0 ?
                    'Your 4 free file uploads across all PDF tools have been used.' :
                    `${remaining} free upload${remaining===1?'':'s'} remaining across all PDF tools.`;
            }

            $('paymentBox').classList.add('hidden');
            message(access.premium ? 'PDF generated successfully with Premium access.' :
                'PDF generated successfully.', true);
            $('result').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

        } catch (error) {
            console.error('Merge PDF error:', error);
            message(error.message || 'Could not merge the selected files. Please check that the PDF files are valid and try again.', false);
        } finally {
            for (const source of pdfSources.values()) {
                if (source.type === 'render') await source.document.destroy();
            }
            $('merge').disabled = false;
            $('merge').textContent = 'Generate PDF';
        }
    };

    $('view').onclick = () => {
        if (!generatedURL) {
            message('Please generate the PDF first.', false);
            return
        }
        $('frame').src = generatedURL;
        $('viewer').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    $('closeViewer').onclick = () => {
        $('viewer').classList.add('hidden');
        $('frame').src = '';
        document.body.classList.remove('overflow-hidden');
    };

    $('download').onclick = () => {
        if (!generatedURL) {
            message('Please generate the PDF first.', false);
            return
        }
        message('PDF downloaded successfully.', true);
    };

    function message(text, success) {
        $('msg').className =
            `mt-5 p-4 rounded-xl text-sm font-semibold ${success?'bg-green-50 text-green-700 border border-green-200':'bg-red-50 text-red-700 border border-red-200'}`;
        $('msg').textContent = text;
        $('msg').classList.remove('hidden');
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        if (bytes < 1073741824) return (bytes / 1048576).toFixed(2) + ' MB';
        return (bytes / 1073741824).toFixed(2) + ' GB';
    }

    function escapeHTML(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    window.addEventListener('beforeunload', () => {
        if (generatedURL) URL.revokeObjectURL(generatedURL);
    });
    </script>
</body>

</html>