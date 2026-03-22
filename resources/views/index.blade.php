<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Translatable Migration Builder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-slate-100">
    <div class="container mx-auto py-8">
        @livewire('translatable-builder.builder')
    </div>

    @livewireScripts

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (data) => {
                alert(data.message);
            });

            Livewire.on('copy-migration', (data) => {
                navigator.clipboard.writeText(data.code).then(() => {
                    alert('Migration code copied to clipboard!');
                });
            });

            Livewire.on('copy-model', (data) => {
                navigator.clipboard.writeText(data.code).then(() => {
                    alert('Model code copied to clipboard!');
                });
            });
        });
    </script>
</body>
</html>
