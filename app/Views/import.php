<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>계좌내역</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://unpkg.com/tabulator-tables/dist/css/tabulator.min.css" rel="stylesheet">
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
                <li class="nav-item">
                    <a href="/import" class="nav-link">매입대장</a>
                </li>
            </ul>
        </div>

        <!-- 본문 -->
        <div class="col-md-11 p-4">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-primary" id="add-row">추가</button>

                <input type="text" class="form-control datepicker" style="width: 150px;" id="add-date">
                <input type="text" class="form-control" style="width: 150px;">
                <input type="text" class="form-control" style="width: 150px;">
            </div>
            <br/>
            <div id="example-table" style="height:800px;"></div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>
<script>
    var dateEditor = function(cell, onRendered, success, cancel){
        //cell - the cell component for the editable cell
        //onRendered - function to call when the editor has been rendered
        //success - function to call to pass thesuccessfully updated value to Tabulator
        //cancel - function to call to abort the edit and return to a normal cell
    
        //create and style input
        var cellValue = luxon.DateTime.fromFormat(cell.getValue(), "dd/MM/yyyy").toFormat("yyyy-MM-dd"),
        input = document.createElement("input");
    
        input.setAttribute("type", "date");
    
        input.style.padding = "4px";
        input.style.width = "100%";
        input.style.boxSizing = "border-box";
    
        input.value = cellValue;
    
        onRendered(function(){
            input.focus();
            input.style.height = "100%";
        });
    
        function onChange(){
            if(input.value != cellValue){
                success(luxon.DateTime.fromFormat(input.value, "yyyy-MM-dd").toFormat("dd/MM/yyyy"));
            }else{
                cancel();
            }
        }
    
        //submit new value on blur or change
        input.addEventListener("blur", onChange);
    
        //submit new value on enter
        input.addEventListener("keydown", function(e){
            if(e.keyCode == 13){
                onChange();
            }
    
            if(e.keyCode == 27){
                cancel();
            }
        });
    
        return input;
    };

    var tableData = [
    {date: "2026-08-23", name:"Billy Bob", age:"12", gender:"male", height:1, col:"red", dob:"", cheese:1},
    {date: "2026-08-20", name:"Mary May", age:"1", gender:"female", height:2, col:"blue", dob:"1982-05-14", cheese:true},
]


var table = new Tabulator("#example-table", {
        data: tableData,
        height:"600px",
        layout:"fitColumns",
        movableRows:true,
        groupBy:["date"],
        groupValues: [
            [
            "2026-08-20",
            "2026-08-22",
            "2026-08-23"
            ]
        ],
        groupUpdateOnCellEdit: true,
        columns:[
            {title:"날짜", field:"date", width:200, editor:"input", cellEdited:function(cell){ able.setGroupBy("date");}},
            {title:"Progress", field:"progress", formatter:"progress", sorter:"number"},
            {title:"Gender", field:"gender", editor:"input"},
            {title:"Rating", field:"rating", formatter:"star", hozAlign:"center", width:100, editor:true},
            {title:"Favourite Color", field:"col"},
            {title:"Date Of Birth", field:"dob", hozAlign:"center", sorter:"date", editor:dateEditor},
            {title:"Driver", field:"car", hozAlign:"center", formatter:"tickCross"},
        ],
    });

document.getElementById("add-row").addEventListener("click", function(){
        //const today = document.getElementById("add-date").value
        table.addRow({date: "2026-08-22", name:"Mary May", age:"1", gender:"male", height:2, col:"blue", dob:"14/05/1982", cheese:true});
    });

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

   });
</script>
</body>
</html>
