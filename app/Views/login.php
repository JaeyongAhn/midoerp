<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fa;
            height: 100vh;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 15px;
        }

        .logo {
            font-size: 3rem;
        }
    </style>
</head>
<body>

<div class="container h-100">
    <div class="row justify-content-center align-items-center h-100">
        <div class="col-md-6 col-lg-4">

            <div class="card shadow login-card">
                <div class="card-body p-5">

                    <div class="text-center mb-4">
                        <div class="logo">🔒</div>
                        <h2 class="mt-2">로그인</h2>
                        <p class="text-muted">계정에 로그인하세요.</p>
                    </div>

                    <form>
                        <div class="mb-3">
                            <label class="form-label">아이디</label>
                            <input
                                id="email"
                                type="text"
                                class="form-control"
                                placeholder="name@example.com"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">비밀번호</label>
                            <input
                                id="password"
                                type="password"
                                class="form-control"
                                placeholder="비밀번호"
                                required>
                        </div>

                        <div class="d-flex justify-content-between mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label" for="remember">
                                    로그인 유지
                                </label>
                            </div>

                            <a href="#">비밀번호 찾기</a>
                        </div>

                        
                    </form>
                    <div class="d-grid">
                        <button id="loginBtn" class="btn btn-primary btn-lg">
                            로그인
                        </button>
                    </div>

                    <hr>

                    <div class="text-center">
                        계정이 없으신가요?
                        <a href="#">회원가입</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
const btn = document.getElementById('loginBtn');

btn.addEventListener('click', function () {
    const data = {
    email: document.getElementById('email').value,
    passwd: document.getElementById('password').value
    };

    fetch('/auth/do_login', {
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
        if(result.code == 100){
            location.href = '/account';
        }else{
            alert('아이디 및 패스워드가 틀렸습니다.')
        }
    })
    .catch(error => {
        console.error('오류:', error);
    });
});
</script>
</body>
</html>