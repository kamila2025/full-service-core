<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>LINE 綁定</title>
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
</head>

<body>
    <form id="bind-form">
        <label>姓名 <input name="name" required></label><br>
        <label>電話 <input name="phone" required></label><br>
        <button type="submit">送出綁定</button>
    </form>
    <script>
        (async () => {
            await liff.init({
                liffId: "{{ config('services.liff.id') }}"
            });
            if (!liff.isLoggedIn()) liff.login();
            const {
                userId
            } = await liff.getProfile();
            document.getElementById('bind-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const fd = new FormData(e.target);
                fd.append('line_user_id', userId);
                const res = await fetch('/api/line/liff/bind', {
                    method: 'POST',
                    body: fd
                });
                alert(res.ok ? '綁定成功' : '綁定失敗');
            });
        })();
    </script>
</body>

</html>
