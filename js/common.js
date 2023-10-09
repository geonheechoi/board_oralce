// common.js

$(document).ready(function() {
    // 글자수 초과 검사 함수
    function checkMaxLength(textareaObj) {
        if (textareaObj.value.length > textareaObj.maxLength) {
            alert('입력 글자 수를 초과하였습니다.');
            textareaObj.value = textareaObj.value.substring(0, textareaObj.maxLength);
        }
    }

    // 입력값이 비어있는지 검사하는 함수
    function isEmpty(value) {
        return !value || value.trim() === "";
    }

    // 게시글 작성 시 입력 검사
    function validateBoardWrite(event) {
        var title = $("#utitle").val().trim();
        var writer = $("#uwriter").val().trim();
        var content = $("#ucontent").val().trim();

        if (isEmpty(title)) {
            alert('제목을 입력해주세요.');
            event.preventDefault();
            return false;
        }
        if (isEmpty(writer)) {
            alert('작성자를 입력해주세요.');
            event.preventDefault();
            return false;
        }
        if (isEmpty(content)) {
            alert('내용을 입력해주세요.');
            event.preventDefault();
            return false;
        }
        return true;
    }

    // 폼이 로드되었을 때 이벤트 핸들러 등록
    var form = $("#writeForm");
    if (form.length > 0) {
        form.on("submit", validateBoardWrite);
    }

    // 검색 버튼 클릭 이벤트 리스너 등록
    var searchButton = $("#searchButton");
    var searchInput = $("#searchInput");
    var searchMessage = $("#searchMessage");

    searchButton.on("click", function(event) {
        if (isEmpty(searchInput.val())) {
            event.preventDefault();
            searchMessage.text("검색어를 입력하세요");
        } else {
            searchMessage.text("");
        }
    });

        // 검색어 입력란의 값이 변경될 때 이벤트 리스너 등록
    searchInput.on("input", function() {
        if (isEmpty(searchInput.val())) {
            searchMessage.text("검색어를 입력하세요");
        } else {
            searchMessage.text("");
        }
    });

    function validateSearch() {
        var searchInput = document.getElementById("searchInput").value.trim();
        if (searchInput === "") {
            alert("검색어를 입력하세요");
            return false; // 검색 중지
        }
        return true; // 검색 실행
    }


    // 추가: 입력란의 글자수 초과 검사 함수 연결
    $("#utitle").on("input", function() {
        checkMaxLength(this);
    });
    $("#uwriter").on("input", function() {
        checkMaxLength(this);
    });
    $("#ucontent").on("input", function() {
        checkMaxLength(this);
    });
    // 검색어 검사 함수 연결
    
    $("#searchButton").on("click", function(event) {
        if (!validateSearch()) {
            event.preventDefault();
        }
    });
    
});
