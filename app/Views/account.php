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
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- 사이드 메뉴 -->
        <div class="col-md-1 sidebar p-3">
            <h4 class="text-white mb-4">메뉴</h4>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="/account" class="nav-link">계좌내역</a>
                </li>
            </ul>
        </div>

        <!-- 본문 -->
        <div class="col-md-11 p-4">

            <!-- 상단 -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">계좌내역</h3>

            <div class="d-flex align-items-center gap-2">
                시작날짜
                <input type="text" id="startDate" class="form-control datepicker" style="width: 180px;">
                마지막날짜
                <input type="text" id="endDate" class="form-control datepicker" style="width: 180px;">
                계정과목
                <select id="searchTag1" class="form-select" multiple style="width: 180px;">
                </select>
                현장
                <select id="searchTag2" class="form-select" multiple style="width: 180px;">
                </select>
                거래처
                <select id="searchTag3" class="form-select" multiple style="width: 180px;">
                </select>
                <button class="btn btn-primary" id="search">
                    검색
                </button>
            </div>
        </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <input
                                type="file"
                                id="excelFile"
                                class="form-control"
                                accept=".xlsx,.xls"
                            >
                        </div>

                        <div class="col-auto">
                            <button id="btnUpload" class="btn btn-success">
                                Excel 업로드
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div id="banner" class="card-body">
                </div>
            </div>

            <!-- 테이블 -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <p id="last_transdate">마지막 내역: <?=esc($last_transdate)?></p>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>거래일시</th>
                                    <th>출금</th>
                                    <th>입금</th>
                                    <th>거래후잔액</th>
                                    <th>거래내용</th>
                                    <th>메모</th>
                                    <th>계정과목<button onclick="openModal(1)">+</button></th>
                                    <th>현장<button onclick="openModal(2)">+</button></th>
                                    <th>거래처<button onclick="openModal(3)">+</button></th>
                                </tr>
                            </thead>
                            <tbody id="table">
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>
<div id="myModal1" class="modal">
    <div class="modal-content" id="tagInputs1">
        <h2>계정과목 입력</h2>
        <input class="tags_name" type="text">
        <button onclick="saveModal(1)">저장</button>
        <button onclick="closeModal(1)">취소</button>
    </div>
</div>
<div id="myModal2" class="modal">
    <div class="modal-content" id="tagInputs2">
        <h2>현장 입력</h2>
        <input class="tags_name" type="text">
        <button onclick="saveModal(2)">저장</button>
        <button onclick="closeModal(2)">취소</button>
    </div>
</div>
<div id="myModal3" class="modal">
    <div class="modal-content" id="tagInputs3">
        <h2>거래처 입력</h2>
        <input class="tags_name" type="text">
        <button onclick="saveModal(3)">저장</button>
        <button onclick="closeModal(3)">취소</button>
    </div>
</div>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    document.getElementById('btnUpload').addEventListener('click', async () => {
    const fileInput = document.getElementById('excelFile');

    if (fileInput.files.length === 0) {
        alert('Excel 파일을 선택하세요.');
        return;
    }

    const formData = new FormData();
    formData.append('excel', fileInput.files[0]);

    try {
        const response = await fetch('/account/do_upload', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('업로드 실패');
        }

        const result = await response.json();
        const data = JSON.parse(result.data);
        document.getElementById('last_transdate').innerHTML = `마지막 내역: ${result.last}`
        document.getElementById('banner').innerHTML = result.banner.replace(/(:.*?)(\s)(?=[^:]*:|$)/g, '$1<br>');;
        document.getElementById('table').innerHTML = '';
        let chulsum = 0;
        let ipsum = 0;
        for(let i=0; i<data.length; i++){
            document.getElementById('table').innerHTML += `<tr>
                <td>${data[i].trans_date}</td>
                <td>${Number(data[i].chulguem).toLocaleString()}</td>
                <td>${Number(data[i].ipguem).toLocaleString()}</td>
                <td>${Number(data[i].janeak).toLocaleString()}</td>
                <td>${data[i].naeyong}</td>
                <td>${data[i].memo==null?'':data[i].memo}</td>
                <td><select class="tagSelect1" class="form-select" data-id="${data[i].id}" data-sel1="${data[i].tag1}" data-sel2="${data[i].tag2}" data-sel3="${data[i].tag3}" multiple>
                </select></td>
                <td><select class="tagSelect2" class="form-select" data-id="${data[i].id}" data-sel1="${data[i].tag1}" data-sel2="${data[i].tag2}" data-sel3="${data[i].tag3}" multiple>
                </select></td>
                <td><select class="tagSelect3" class="form-select" data-id="${data[i].id}" data-sel1="${data[i].tag1}" data-sel2="${data[i].tag2}" data-sel3="${data[i].tag3}" multiple>
                </select></td>
            <tr>`
            chulsum += Number(data[i].chulguem);
            ipsum += Number(data[i].ipguem);
        }
        document.getElementById('table').innerHTML += `<tr>
            <td>합계</td>
            <td>${Number(chulsum).toLocaleString()}</td>
            <td>${Number(ipsum).toLocaleString()}</td>
            <td></td><td></td><td></td><td></td><td></td><td></td>
            `
        $('.tagSelect1').select2({
            placeholder: '태그를 선택하세요.',
            allowClear: true,
            width: '150px'
        });

        $('.tagSelect2').select2({
            placeholder: '태그를 선택하세요.',
            allowClear: true,
            width: '150px'
        });

        $('.tagSelect3').select2({
            placeholder: '태그를 선택하세요.',
            allowClear: true,
            width: '150px'
        });

        $('.tagSelect1').on('change', function() {
            let values = $(this).val();
            if(values) values = values.join(',');
            const id = $(this).data('id');
            const tags = {'tag':values, 'id':id, 'mode':1}
            fetch('/account/save_tag', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(tags)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
            })
            .catch(error => {
                console.error('오류:', error);
            });
        });

        $('.tagSelect2').on('change', function() {
            let values = $(this).val();
            if(values) values = values.join(',');
            const id = $(this).data('id');
            const tags = {'tag':values, 'id':id, 'mode':2}
            fetch('/account/save_tag', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(tags)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
            })
            .catch(error => {
                console.error('오류:', error);
            });
        });

        $('.tagSelect3').on('change', function() {
            let values = $(this).val();
            if(values) values = values.join(',');
            const id = $(this).data('id');
            const tags = {'tag':values, 'id':id, 'mode':3}
            fetch('/account/save_tag', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(tags)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
            })
            .catch(error => {
                console.error('오류:', error);
            });
        });

        {
            const response1 = await fetch(`/account/load_tags?mode=1`, {
                method: 'GET'
            });
            const result1 = await response1.json();
            const tags = result1.data
                .map(tag => tag.name);

            const selects = document.querySelectorAll('.tagSelect1');

            selects.forEach(select => {
                tags.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;

                    select.appendChild(option);
                });
            })
        }

        {
            const response1 = await fetch(`/account/load_tags?mode=2`, {
                method: 'GET'
            });
            const result1 = await response1.json();
            const tags = result1.data
                .map(tag => tag.name);

            const selects = document.querySelectorAll('.tagSelect2');

            selects.forEach(select => {
                tags.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;

                    select.appendChild(option);
                });
            })
        }

        {
            const response1 = await fetch(`/account/load_tags?mode=3`, {
                method: 'GET'
            });
            const result1 = await response1.json();
            const tags = result1.data
                .map(tag => tag.name);

            const selects = document.querySelectorAll('.tagSelect3');

            selects.forEach(select => {
                tags.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;

                    select.appendChild(option);
                });
            })
        }

        $('.tagSelect1').each(function () {
            const sel = $(this).data('sel1'); // data-sel 값

            if (sel) {
                const values = sel.split(','); // "1,3,4" → ["1","3","4"]

                $(this).val(values).trigger('change');
            }
        });

        $('.tagSelect2').each(function () {
            const sel = $(this).data('sel2'); // data-sel 값

            if (sel) {
                const values = sel.split(','); // "1,3,4" → ["1","3","4"]

                $(this).val(values).trigger('change');
            }
        });

        $('.tagSelect3').each(function () {
            const sel = $(this).data('sel3'); // data-sel 값

            if (sel) {
                const values = sel.split(','); // "1,3,4" → ["1","3","4"]

                $(this).val(values).trigger('change');
            }
        });

    } catch (err) {
        console.error(err);
        alert('업로드 중 오류가 발생했습니다.');
    }

    
});

document.getElementById('search').addEventListener('click', async () => {
    
    const startDate = document.getElementById('startDate').value
    const endDate = document.getElementById('endDate').value
    const tag1 = $("#searchTag1").val();
    const tag2 = $("#searchTag2").val();
    const tag3 = $("#searchTag3").val();

    fetch('/account/search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({startDate, endDate, tag1, tag2, tag3})
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(async result => {
       const data = JSON.parse(result.data);
        
        document.getElementById('table').innerHTML = '';
        let chulsum = 0;
        let ipsum = 0;
        for(let i=0; i<data.length; i++){
            document.getElementById('table').innerHTML += `<tr>
                <td>${data[i].trans_date}</td>
                <td>${Number(data[i].chulguem).toLocaleString()}</td>
                <td>${Number(data[i].ipguem).toLocaleString()}</td>
                <td>${Number(data[i].janeak).toLocaleString()}</td>
                <td>${data[i].naeyong}</td>
                <td>${data[i].memo==null?'':data[i].memo}</td>
                <td><select class="tagSelect1" class="form-select" data-id="${data[i].id}" data-sel1="${data[i].tag1}" data-sel2="${data[i].tag2}" data-sel3="${data[i].tag3}" multiple>
                </select></td>
                <td><select class="tagSelect2" class="form-select" data-id="${data[i].id}" data-sel1="${data[i].tag1}" data-sel2="${data[i].tag2}" data-sel3="${data[i].tag3}" multiple>
                </select></td>
                <td><select class="tagSelect3" class="form-select" data-id="${data[i].id}" data-sel1="${data[i].tag1}" data-sel2="${data[i].tag2}" data-sel3="${data[i].tag3}" multiple>
                </select></td>
            <tr>`
            chulsum += Number(data[i].chulguem);
            ipsum += Number(data[i].ipguem);
        }
        document.getElementById('table').innerHTML += `<tr>
            <td>합계</td>
            <td>${Number(chulsum).toLocaleString()}</td>
            <td>${Number(ipsum).toLocaleString()}</td>
            <td></td><td></td><td></td><td></td><td></td><td></td>
            `

                
        $('.tagSelect1').select2({
            placeholder: '태그를 선택하세요.',
            allowClear: true,
            width: '150px'
        });

        $('.tagSelect2').select2({
            placeholder: '태그를 선택하세요.',
            allowClear: true,
            width: '150px'
        });

        $('.tagSelect3').select2({
            placeholder: '태그를 선택하세요.',
            allowClear: true,
            width: '150px'
        });

        $('.tagSelect1').on('change', function() {
            let values = $(this).val();
            if(values) values = values.join(',');
            const id = $(this).data('id');
            const tags = {'tag':values, 'id':id, 'mode':1}
            fetch('/account/save_tag', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(tags)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
            })
            .catch(error => {
                console.error('오류:', error);
            });
        });

        $('.tagSelect2').on('change', function() {
            let values = $(this).val();
            if(values) values = values.join(',');
            const id = $(this).data('id');
            const tags = {'tag':values, 'id':id, 'mode':2}
            fetch('/account/save_tag', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(tags)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
            })
            .catch(error => {
                console.error('오류:', error);
            });
        });

        $('.tagSelect3').on('change', function() {
            let values = $(this).val();
            if(values) values = values.join(',');
            const id = $(this).data('id');
            const tags = {'tag':values, 'id':id, 'mode':3}
            fetch('/account/save_tag', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(tags)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
            })
            .catch(error => {
                console.error('오류:', error);
            });
        });

        {
            const response1 = await fetch('/account/load_tags?mode=1', {
                method: 'GET'
            });
            const result1 = await response1.json();
            const tags = result1.data
                .map(tag => tag.name);

            const selects = document.querySelectorAll('.tagSelect1');

            selects.forEach(select => {
                tags.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;

                    select.appendChild(option);
                });
            })
            $('.tagSelect1').each(function () {
                const sel = $(this).data('sel1'); // data-sel 값

                if (sel) {
                    const values = sel.split(','); // "1,3,4" → ["1","3","4"]

                    $(this).val(values).trigger('change');
                }
            });
        }

        {
            const response1 = await fetch('/account/load_tags?mode=2', {
                method: 'GET'
            });
            const result1 = await response1.json();
            const tags = result1.data
                .map(tag => tag.name);

            const selects = document.querySelectorAll('.tagSelect2');

            selects.forEach(select => {
                tags.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;

                    select.appendChild(option);
                });
            })
            $('.tagSelect2').each(function () {
                const sel = $(this).data('sel2'); // data-sel 값

                if (sel) {
                    const values = sel.split(','); // "1,3,4" → ["1","3","4"]

                    $(this).val(values).trigger('change');
                }
            });
        }

        {
            const response1 = await fetch('/account/load_tags?mode=3', {
                method: 'GET'
            });
            const result1 = await response1.json();
            const tags = result1.data
                .map(tag => tag.name);

            const selects = document.querySelectorAll('.tagSelect3');

            selects.forEach(select => {
                tags.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;

                    select.appendChild(option);
                });
            })
            $('.tagSelect3').each(function () {
                const sel = $(this).data('sel3'); // data-sel 값

                if (sel) {
                    const values = sel.split(','); // "1,3,4" → ["1","3","4"]

                    $(this).val(values).trigger('change');
                }
            });
        }

    })
    .catch(error => {
        console.error('오류:', error);
    });
});
</script>
<script>
const containers = [document.getElementById('tagInputs1'), document.getElementById('tagInputs2'), document.getElementById('tagInputs3')]
const saveButtons = [containers[0].querySelectorAll('button')[0],containers[1].querySelectorAll('button')[0],containers[2].querySelectorAll('button')[0]]

function setTags(tags, mode) {
    // 기존 input 제거
    containers[mode-1].querySelectorAll('input').forEach(input => input.remove());

    // 배열만큼 input 생성
    tags.forEach(tag => {
        const input = document.createElement('input');
        input.type = 'text';
        input.value = tag;
        input.disabled = true;

        containers[mode-1].insertBefore(input, saveButtons[mode-1]);
    });

    // 마지막 빈 input 하나 추가
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'tags_name';
    containers[mode-1].insertBefore(input, saveButtons[mode-1]);
}

async function openModal(mode) {
    const response = await fetch(`/account/load_tags?mode=${mode}`, {
            method: 'GET'
        });
    const result = await response.json();
    const tags = result.data
        .map(tag => tag.name);
    setTags(tags, mode);
    bindLastInput(mode);

    document.getElementById("myModal"+mode).style.display = "block";
}

function saveModal(mode) {
    const tags = Array.from(document.querySelectorAll(`#tagInputs${mode} .tags_name`))
    .filter(input => !input.disabled)
    .map(input => input.value.trim())
    .filter(value => value !== '');

    const data = {
        'tags': tags,
        'mode': mode
    }
    fetch('/account/save_tags', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(result => {
        if(result.success == false){
            alert(result.item+' 태그가 중복입니다')
        }else{
            const selects = document.querySelectorAll('.tagSelect'+mode);
            
            selects.forEach(select => {
                tags.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;

                    select.appendChild(option);
                });
            })
            const select = document.getElementById('searchTag'+mode)
            tags.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;

                    select.appendChild(option);
                });
        }
    })
    .catch(error => {
        console.error('오류:', error);
    });

    document.getElementById("myModal"+mode).style.display = "none";
}

function closeModal(mode) {
    document.getElementById("myModal"+mode).style.display = "none";
}

function bindLastInput(mode) {
    const inputs = containers[mode-1].querySelectorAll('input');
    const lastInput = inputs[inputs.length - 1];

    lastInput.oninput = function () {
        if (this.value.trim() === '') return;

        // 현재 input에서는 더 이상 실행되지 않도록 제거
        this.oninput = null;

        // 새 input 생성
        const newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.className = 'tags_name';

        containers[mode-1].insertBefore(newInput, saveButtons[mode-1]);

        // 새 마지막 input에만 이벤트 등록
        bindLastInput(mode);
    };
}

bindLastInput(1);
bindLastInput(2);
bindLastInput(3);
</script>
<script>
    async function loadSearchTag(){
        {
            $('#searchTag1').select2({
                placeholder: '태그를 선택하세요.',
                allowClear: true,
                width: '180px'
            });

            const response1 = await fetch('/account/load_tags?mode=1', {
                method: 'GET'
            });
            const result1 = await response1.json();
            const tags = result1.data
                .map(tag => tag.name);
            
            const select = document.querySelector('#searchTag1');

            tags.forEach(item => {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;

                select.appendChild(option);
            });
        }
        {
            $('#searchTag2').select2({
                placeholder: '태그를 선택하세요.',
                allowClear: true,
                width: '180px'
            });

            const response1 = await fetch('/account/load_tags?mode=2', {
                method: 'GET'
            });
            const result1 = await response1.json();
            const tags = result1.data
                .map(tag => tag.name);
            
            const select = document.querySelector('#searchTag2');

            tags.forEach(item => {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;

                select.appendChild(option);
            });
        }

        {
            $('#searchTag3').select2({
                placeholder: '태그를 선택하세요.',
                allowClear: true,
                width: '180px'
            });

            const response1 = await fetch('/account/load_tags?mode=3', {
                method: 'GET'
            });
            const result1 = await response1.json();
            const tags = result1.data
                .map(tag => tag.name);
            
            const select = document.querySelector('#searchTag3');

            tags.forEach(item => {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;

                select.appendChild(option);
            });
        }
    }
   $(function() {
           //input을 datepicker로 선언
       $(".datepicker").datepicker({
           dateFormat: 'yy-mm-dd' //달력 날짜 형태
           ,showOtherMonths: true //빈 공간에 현재월의 앞뒤월의 날짜를 표시
           ,showMonthAfterYear:true // 월- 년 순서가아닌 년도 - 월 순서
           ,changeYear: true //option값 년 선택 가능
           ,changeMonth: true //option값  월 선택 가능                
           //,showOn: "both" //button:버튼을 표시하고,버튼을 눌러야만 달력 표시 ^ both:버튼을 표시하고,버튼을 누르거나 input을 클릭하면 달력 표시  
           //,buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif" //버튼 이미지 경로
           ,buttonImageOnly: true //버튼 이미지만 깔끔하게 보이게함
           ,buttonText: "선택" //버튼 호버 텍스트              
           ,yearSuffix: "년" //달력의 년도 부분 뒤 텍스트
           ,monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'] //달력의 월 부분 텍스트
           ,monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'] //달력의 월 부분 Tooltip
           ,dayNamesMin: ['일','월','화','수','목','금','토'] //달력의 요일 텍스트
           ,dayNames: ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'] //달력의 요일 Tooltip
           ,minDate: "-5Y" //최소 선택일자(-1D:하루전, -1M:한달전, -1Y:일년전)
           ,maxDate: "+5y" //최대 선택일자(+1D:하루후, -1M:한달후, -1Y:일년후)  
       });                    
       
       //초기값을 오늘 날짜로 설정해줘야 합니다.
       $('#datepicker').datepicker('setDate', 'today'); //(-1D:하루전, -1M:한달전, -1Y:일년전), (+1D:하루후, -1M:한달후, -1Y:일년후)            

        loadSearchTag();
   });
</script>
</body>
</html>