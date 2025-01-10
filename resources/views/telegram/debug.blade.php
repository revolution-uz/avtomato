🐞<b> Error!</b>
📂<b> File:</b> {{ $file }}
➡️<b> Line:</b> {{ $line }}
📨<b> Message:</b> {{ $message }}
🌐<b> SERVER HOST:</b> {{ $_SERVER['HTTP_HOST'] ?? '' }} {{ $_SERVER['REQUEST_URI'] ?? '' }}
🌐<b> SERVER IP:</b> {{ $_SERVER['SERVER_ADDR'] ?? ($_SERVER['SERVER_NAME'] ?? '') }}
🔌<b> SERVER PORT:</b> {{ $_SERVER['SERVER_PORT'] ?? '' }}
🧾<b> APP_ENV:</b> {{ config('app.env') ?? '' }}
🧭<b> GETHOSTNAME:</b> {{ gethostname() }}
🌍<b> CLIENT IP/BROWSER:</b> {{ $_SERVER['HTTP_USER_AGENT'] ?? 'Console' }}
⌚️<b> TIME:</b> {{ \Illuminate\Support\Carbon::now() }}
