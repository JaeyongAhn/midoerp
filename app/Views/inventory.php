<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>계좌내역</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }

        .sidebar .nav-link {
            color: #fff;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        
    </style>
    <style>
        .modal {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        /* 모달 내용 */
        .modal-content {
            background: white;
            width: 400px;
            max-height: 500px;      /* 최대 높이 지정 */
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;

            overflow-y: auto;       /* 내용이 넘치면 세로 스크롤 */
            overflow-x: hidden;     /* 가로 스크롤 숨김 */
        }
    </style>
    <style>
        :root {
            --ink: #12203d;
            --line: #e1e7ef
        }

        body {
            font-family: system-ui, -apple-system, 'Malgun Gothic', sans-serif;
            color: var(--ink);
            background: #fcfdff
        }

        button,
        input,
        select {
            font: inherit
        }

        .layout {
            display: flex;
            min-height: 100vh
        }

        .sidebar {
            width: 224px;
            flex-shrink: 0;
            background: #f7f9fc;
            border-right: 1px solid var(--line);
            padding: 32px 18px
        }

        .brand {
            font-size: 27px;
            font-weight: 800;
            margin: 0 10px 35px
        }

        .nav-link {
            color: #66738c;
            padding: 16px;
            border-radius: 8px;
            font-weight: 600
        }

        .nav-link.active {
            background: #e6efff;
            color: #1765ed;
            border-left: 3px solid #1765ed
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 44px 38px
        }

        .page-title {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -1.5px
        }

        .subtitle {
            color: #78849a
        }

        .toolbar .btn {
            padding: 12px 19px;
            font-weight: 700;
            white-space: nowrap
        }

        .btn-in {
            color: #087c82;
            background: #e9f8f8;
            border-color: #a6dadc
        }

        .btn-out {
            color: #cf641e;
            background: #fff3e9;
            border-color: #f0c5a5
        }

        .stat {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            background: white;
            height: 126px
        }

        .stat-icon {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: #edf4ff;
            color: #1765ed;
            font-size: 26px
        }

        .stat-label {
            color: #78849a;
            margin-bottom: 4px
        }

        .stat-value {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.2
        }

        .form-control,
        .form-select {
            border-color: var(--line);
            padding: 13px 16px
        }

        .table-box {
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: hidden;
            background: white
        }

        .table {
            margin: 0;
            white-space: nowrap;
            vertical-align: middle
        }

        .table>:not(caption)>*>* {
            padding: 22px 20px;
            border-color: #e8edf4;
            color: var(--ink)
        }

        .table thead th {
            background: #f3f6fa;
            font-size: 14px;
            font-weight: 700
        }

        .table tbody tr.selected td {
            background: #edf4ff
        }

        .quantity {
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            text-align: right
        }

        .badge-status {
            display: inline-block;
            min-width: 74px;
            padding: 7px 12px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
            background: #dcf6e6;
            color: #19804e
        }

        .badge-status.low {
            background: #fff0bd;
            color: #976018
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            cursor: pointer
        }

        .selection-note {
            font-size: 13px;
            color: #75839a
        }

        .footnote {
            font-size: 12px;
            color: #8d98aa;
            margin-top: 26px
        }

        .modal-content {
            border: 0;
            border-radius: 14px
        }

        .modal-header,
        .modal-footer {
            border-color: var(--line)
        }

        .modal-body {
            padding: 24px
        }

        .empty {
            text-align: center;
            padding: 60px !important;
            color: #7c879a !important
        }

        .save-state {
            font-size: 12px;
            color: #768299
        }

        .toast-container {
            z-index: 2000
        }

        .history-panel {
            display: none
        }

        .section-heading {
            font-weight: 700;
            font-size: 22px
        }

        @media(min-width:1500px) {
            .main {
                padding: 48px 48px
            }
        }

        @media(max-width:1100px) {
            .sidebar {
                width: 185px
            }

            .main {
                padding: 28px 24px
            }

            .toolbar {
                margin-top: 12px
            }
        }

        @media(max-width:767px) {
            .layout {
                display: block
            }

            .sidebar {
                width: 100%;
                padding: 16px;
                border-right: 0;
                border-bottom: 1px solid var(--line)
            }

            .brand {
                font-size: 20px;
                margin: 0 0 12px
            }

            .sidebar .nav {
                flex-direction: row !important;
                gap: 8px
            }

            .nav-link {
                padding: 9px 12px;
                font-size: 13px
            }

            .main {
                padding: 24px 16px
            }

            .page-title {
                font-size: 29px
            }

            .toolbar {
                width: 100%;
                display: grid !important;
                grid-template-columns: 1fr 1fr
            }

            .toolbar .btn {
                padding: 11px 10px
            }

            .stat {
                height: 100px;
                padding: 16px;
                gap: 14px
            }

            .stat-value {
                font-size: 25px
            }

            .table>:not(caption)>*>* {
                padding: 16px
            }

            .footnote {
                line-height: 1.7
            }
        }
    </style>
</head>
<body>
<div class="layout">
        <aside class="sidebar">
            <div class="brand">재고 관리</div>
            <nav class="nav flex-column gap-2" aria-label="주 메뉴"><button class="nav-link active text-start border-0"
                    id="stockNav">▣ &nbsp; 현재 재고</button><button class="nav-link text-start border-0" id="historyNav">▤
                    &nbsp; 입출고 내역</button></nav>
        </aside>
        <main class="main">
            <header class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h1 class="page-title" id="pageTitle">현재 재고</h1>
                    <p class="subtitle mb-0" id="pageSubtitle">품목별 재고 현황을 확인하고 관리하세요.</p>
                </div>
                <div class="d-flex flex-wrap gap-2 toolbar"><button class="btn btn-outline-primary" id="addBtn">＋
                        재고항목추가</button><button class="btn btn-primary" id="saveBtn">▣ &nbsp; 저장</button><button
                        class="btn btn-in" id="inBtn">＋ &nbsp; 입고</button><button class="btn btn-out" id="outBtn">−
                        &nbsp; 출고</button></div>
            </header>
            <section id="stockPanel">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="stat">
                            <div class="stat-icon" aria-hidden="true">▣</div>
                            <div>
                                <div class="stat-label">전체 품목</div>
                                <div class="stat-value" id="totalItems">6</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat">
                            <div class="stat-icon" style="background:#e0f6e8;color:#11865c" aria-hidden="true">≡</div>
                            <div>
                                <div class="stat-label">재고 보유 품목</div>
                                <div class="stat-value" id="availableItems">6</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat">
                            <div class="stat-icon" style="background:#fff0dc;color:#d37719" aria-hidden="true">⚠</div>
                            <div>
                                <div class="stat-label">재고 부족</div>
                                <div class="stat-value" id="lowItems">1</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-lg-6"><label class="visually-hidden" for="search">품목 검색</label><input id="search"
                            class="form-control" type="search" placeholder="품목명 또는 품목코드 검색"></div>
                    <div class="col-6 col-lg-3"><label class="visually-hidden" for="category">분류</label><select
                            id="category" class="form-select">
                            <option value="">전체 분류</option>
                        </select></div>
                    <div class="col-6 col-lg-3"><label class="visually-hidden" for="location">보관 위치</label><select
                            id="location" class="form-select">
                            <option value="">전체 보관 위치</option>
                        </select></div>
                </div>
                <div class="d-flex justify-content-between mb-2"><span class="selection-note" id="selectionNote">품목을
                        선택하면 입고·출고할 수 있습니다.</span><span class="save-state" id="saveState" role="status">변경사항 없음</span>
                </div>
                <div class="table-box table-responsive">
                    <table class="table">
                        <caption class="visually-hidden">현재 재고 목록</caption>
                        <thead>
                            <tr>
                                <th style="width:55px">선택</th>
                                <th>품목코드</th>
                                <th>품목명</th>
                                <th>규격</th>
                                <th class="text-end">현재 재고</th>
                                <th>단위</th>
                                <th>보관 위치</th>
                                <th>상태</th>
                            </tr>
                        </thead>
                        <tbody id="rows"></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3"><span class="subtitle small"
                        id="count"></span><span class="badge bg-primary rounded-2 px-3 py-2">1</span></div>
            </section>
            <section class="history-panel" id="historyPanel">
                <h2 class="section-heading mb-3">입출고 내역</h2>
                <div class="table-box table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>일시</th>
                                <th>유형</th>
                                <th>품목명</th>
                                <th>수량</th>
                                <th>처리 후 재고</th>
                                <th>메모</th>
                            </tr>
                        </thead>
                        <tbody id="historyRows"></tbody>
                    </table>
                </div>
            </section>
            <p class="footnote">데모 화면 · 저장한 데이터는 현재 브라우저에 보관됩니다. 입고·출고 후 저장 버튼을 눌러주세요.</p>
        </main>
    </div>
    <div class="modal fade" id="editor" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" id="itemForm">
                <div class="modal-header">
                    <h2 class="modal-title fs-5 fw-bold" id="modalTitle">재고항목 추가</h2><button type="button"
                        class="btn-close" data-bs-dismiss="modal" aria-label="닫기"></button>
                </div>
                <div class="modal-body">
                    <div id="fields"></div>
                    <p id="formError" class="text-danger small mt-3 mb-0" role="alert"></p>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light"
                        data-bs-dismiss="modal">취소</button><button class="btn btn-primary" type="submit"
                        id="submitBtn">추가</button></div>
            </form>
        </div>
    </div>
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="notice" class="toast align-items-center text-bg-dark border-0" role="status" aria-live="polite">
            <div class="d-flex">
                <div class="toast-body" id="noticeText"></div><button type="button"
                    class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="닫기"></button>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script>
        const KEY = 'inventory-bootstrap-v1';
        const seed = [['ITM-001', '볼트', 'M10×50', 1250, '개', 'A-01', '부품', 100], ['ITM-002', '너트', 'M10', 860, '개', 'A-02', '부품', 100], ['ITM-003', '철판', '3T×1000', 24, '장', 'B-01', '자재', 10], ['ITM-004', 'PVC 파이프', '25mm', 12, '개', 'B-03', '자재', 20], ['ITM-005', '케이블', '2.5SQ', 320, 'm', 'C-01', '전기', 50], ['ITM-006', '안전장갑', 'L', 86, '켤레', 'C-02', '안전용품', 20]].map(([code, name, spec, qty, unit, location, category, min]) => ({ code, name, spec, qty, unit, location, category, min }));
        let items = structuredClone(seed), history = [], selected = null, dirty = false, mode = 'add';
        const $ = id => document.getElementById(id), fmt = n => n.toLocaleString('ko-KR'), esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        try { const saved = JSON.parse(localStorage.getItem(KEY)); if (saved && Array.isArray(saved.items) && saved.items.every(i => typeof i.code === 'string' && typeof i.name === 'string' && Number.isFinite(i.qty) && i.qty >= 0 && Number.isFinite(i.min) && i.min >= 0)) { items = saved.items; history = Array.isArray(saved.history) ? saved.history : [] } } catch { }
        function notify(message) { $('noticeText').textContent = message; if (window.bootstrap) bootstrap.Toast.getOrCreateInstance($('notice')).show() }
        function markDirty() { dirty = true; $('saveState').textContent = '저장하지 않은 변경사항'; $('saveState').style.color = '#cb741c' }
        function filters() { for (const [id, key, label] of [['category', 'category', '전체 분류'], ['location', 'location', '전체 보관 위치']]) { const old = $(id).value; $(id).innerHTML = '<option value="">' + label + '</option>' + [...new Set(items.map(i => i[key]))].sort().map(v => `<option value="${esc(v)}">${esc(v)}</option>`).join(''); $(id).value = old } }
        function render() { const q = $('search').value.trim().toLowerCase(); const visible = items.filter(i => (i.name.toLowerCase().includes(q) || i.code.toLowerCase().includes(q)) && (!$('category').value || i.category === $('category').value) && (!$('location').value || i.location === $('location').value)); $('rows').innerHTML = visible.length ? visible.map(i => `<tr class="${selected === i.code ? 'selected' : ''}"><td><input class="form-check-input" type="checkbox" aria-label="${esc(i.name)} 선택" data-code="${esc(i.code)}" ${selected === i.code ? 'checked' : ''}></td><td>${esc(i.code)}</td><td>${esc(i.name)}</td><td>${esc(i.spec)}</td><td class="quantity">${fmt(i.qty)}</td><td>${esc(i.unit)}</td><td>${esc(i.location)}</td><td><span class="badge-status ${i.qty < i.min ? 'low' : ''}">${i.qty < i.min ? '재고 부족' : '정상'}</span></td></tr>`).join('') : '<tr><td colspan="8" class="empty">검색 결과가 없습니다.</td></tr>'; $('totalItems').textContent = fmt(items.length); $('availableItems').textContent = fmt(items.filter(i => i.qty > 0).length); $('lowItems').textContent = fmt(items.filter(i => i.qty < i.min).length); $('count').textContent = `총 ${fmt(items.length)}개 품목 · ${fmt(visible.length)}개 표시`; $('selectionNote').textContent = selected ? `${items.find(i => i.code === selected)?.name} 선택됨` : '품목을 선택하면 입고·출고할 수 있습니다.'; renderHistory() }
        function renderHistory() { $('historyRows').innerHTML = history.length ? [...history].reverse().map(h => `<tr><td>${esc(h.date)}</td><td><span class="badge-status ${h.type === '출고' ? 'low' : ''}">${esc(h.type)}</span></td><td>${esc(h.name)}</td><td>${fmt(h.qty)} ${esc(h.unit)}</td><td class="fw-bold">${fmt(h.after)}</td><td>${esc(h.memo)}</td></tr>`).join('') : '<tr><td colspan="6" class="empty">아직 입출고 내역이 없습니다.</td></tr>' }
        function field(name, label, type = 'text', value = '', extra = '') { return `<div class="mb-3"><label for="f-${name}" class="form-label small fw-semibold">${label}</label><input id="f-${name}" name="${name}" type="${type}" value="${esc(value)}" class="form-control" required ${extra}></div>` }
        function openEditor(next) { if (!window.bootstrap) { alert('Bootstrap을 불러오지 못했습니다. 인터넷 연결을 확인해 주세요.'); return } mode = next; $('formError').textContent = ''; if (mode === 'add') { $('modalTitle').textContent = '재고항목 추가'; $('submitBtn').textContent = '추가'; $('fields').innerHTML = '<div class="row"><div class="col-6">' + field('code', '품목코드') + '</div><div class="col-6">' + field('name', '품목명') + '</div></div>' + field('spec', '규격') + '<div class="row"><div class="col-6">' + field('qty', '초기 재고', 'number', 0, 'min="0" step="any"') + '</div><div class="col-6">' + field('unit', '단위', 'text', '개') + '</div></div><div class="row"><div class="col-6">' + field('location', '보관 위치') + '</div><div class="col-6">' + field('category', '분류') + '</div></div>' + field('min', '최소 재고 기준', 'number', 10, 'min="0" step="any"') } else { const i = items.find(i => i.code === selected); if (!i) { notify('먼저 입고·출고할 품목을 선택해 주세요.'); return } $('modalTitle').textContent = mode === 'in' ? '입고 등록' : '출고 등록'; $('submitBtn').textContent = mode === 'in' ? '입고 처리' : '출고 처리'; $('fields').innerHTML = `<div class="p-3 rounded bg-light mb-4"><strong>${esc(i.name)}</strong><div class="text-secondary small mt-1">${esc(i.code)} · 현재 재고 ${fmt(i.qty)} ${esc(i.unit)}</div></div>` + field('qty', '처리 수량', 'number', '', 'min="0.000001" step="any"') + '<label for="memo" class="form-label small fw-semibold">메모 (선택)</label><textarea id="memo" name="memo" class="form-control" rows="2" placeholder="입출고 사유를 입력하세요"></textarea>' } bootstrap.Modal.getOrCreateInstance($('editor')).show() }
        $('itemForm').addEventListener('submit', e => { e.preventDefault(); const data = Object.fromEntries(new FormData(e.target)); const qty = Number(data.qty); if (!Number.isFinite(qty) || qty < 0 || (mode !== 'add' && qty <= 0)) { $('formError').textContent = '유효한 수량을 입력해 주세요.'; return } if (mode === 'add') { for (const key of ['code', 'name', 'spec', 'unit', 'location', 'category']) { data[key] = data[key].trim(); if (!data[key]) { $('formError').textContent = '필수 항목을 입력해 주세요.'; return } } if (items.some(i => i.code.toLowerCase() === data.code.toLowerCase())) { $('formError').textContent = '이미 등록된 품목코드입니다.'; return } const min = Number(data.min); if (!Number.isFinite(min) || min < 0) return; items.push({ ...data, qty, min }); selected = data.code; filters() } else { const i = items.find(i => i.code === selected); if (mode === 'out' && qty > i.qty) { $('formError').textContent = '현재 재고보다 많이 출고할 수 없습니다.'; return } const after = Number((i.qty + (mode === 'in' ? qty : -qty)).toFixed(6)); if (!Number.isFinite(after)) { $('formError').textContent = '수량이 너무 큽니다.'; return } i.qty = after; history.push({ date: new Date().toLocaleString('ko-KR'), type: mode === 'in' ? '입고' : '출고', name: i.name, qty, unit: i.unit, after: i.qty, memo: data.memo.trim() }) } markDirty(); render(); bootstrap.Modal.getInstance($('editor')).hide(); notify('반영되었습니다. 저장 버튼을 눌러주세요.') });
        $('rows').addEventListener('change', e => { if (e.target.dataset.code) { selected = e.target.checked ? e.target.dataset.code : null; render() } });
        for (const id of ['search', 'category', 'location']) $(id).addEventListener(id === 'search' ? 'input' : 'change', render);
        $('addBtn').onclick = () => openEditor('add'); $('inBtn').onclick = () => openEditor('in'); $('outBtn').onclick = () => openEditor('out'); $('saveBtn').onclick = () => { try { localStorage.setItem(KEY, JSON.stringify({ items, history })); dirty = false; $('saveState').textContent = '저장 완료 · ' + new Date().toLocaleTimeString('ko-KR'); $('saveState').style.color = '#19804e'; notify('현재 브라우저에 저장했습니다.') } catch { notify('저장하지 못했습니다. 브라우저 저장 공간을 확인해 주세요.') } };
        function navigate(showHistory) { $('stockPanel').style.display = showHistory ? 'none' : 'block'; $('historyPanel').style.display = showHistory ? 'block' : 'none'; $('stockNav').classList.toggle('active', !showHistory); $('historyNav').classList.toggle('active', showHistory); $('pageTitle').textContent = showHistory ? '입출고 내역' : '현재 재고'; $('pageSubtitle').textContent = showHistory ? '품목별 입고·출고 기록을 확인하세요.' : '품목별 재고 현황을 확인하고 관리하세요.' }
        $('stockNav').onclick = () => navigate(false); $('historyNav').onclick = () => navigate(true); window.addEventListener('beforeunload', e => { if (dirty) { e.preventDefault(); e.returnValue = '' } }); $('editor').addEventListener('shown.bs.modal', () => { $('fields').querySelector('input')?.focus() }); filters(); render();
    </script>
</body>
</html>