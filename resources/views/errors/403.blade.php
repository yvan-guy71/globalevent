<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accès refusé - Event Global</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-lg w-full text-center">
        <div class="text-7xl font-black text-rose-600 mb-4">403</div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white mb-3">Accès refusé</h1>
        <p class="text-slate-600 dark:text-slate-300 mb-8">Vous n'avez pas l'autorisation d'accéder à cette page. Connectez-vous avec un compte disposant des droits nécessaires.</p>
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700">Retour à l'accueil</a>
    </div>
</body>
</html>