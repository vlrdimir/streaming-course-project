<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - Dicoding Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#0ea5e9',
                        secondary: '#64748b',
                        dark: '#1e293b',
                        darker: '#0f172a',
                    },
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .scrollbar-thin {
                scrollbar-width: thin;
            }
            .scrollbar-dark::-webkit-scrollbar {
                width: 8px;
            }
            .scrollbar-dark::-webkit-scrollbar-track {
                background: #1e293b;
            }
            .scrollbar-dark::-webkit-scrollbar-thumb {
                background-color: #475569;
                border-radius: 6px;
            }
        }
    </style>
    
    <style>
        

.academy-tutorial-content {
    background-color: #1a1a1a;
    color: white;
    padding: 20px;
    border-radius: 5px;
}

.academy-tutorial-content a {
    color: #61dafb; /* React blue color */
}

.academy-tutorial-content blockquote {
    background-color: rgba(255, 255, 255, 0.1);
    border-left: 4px solid #61dafb;
    padding: 10px 15px;
    margin: 15px 0;
}

.zoomable-image-anchor {
    width: 100%;

}
.zoomable-image-anchor img {
    cursor: pointer;
    max-width: 50%;
    margin: 0 auto;
    /* padding y */
    padding: 20px 0;
    transition: transform 0.3s ease;
}

.zoomable-image-anchor img:hover {
    transform: scale(1.02);
}
            </style>
</head>
<body class="bg-darker text-white">
    <header class="bg-dark border-b border-gray-700">
        <div class=" px-4 py-3 flex items-center justify-between">
            <div class="flex items-center">
                <a href="/courses" class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-lg font-medium"><?= esc($title) ?></span>
                </a>
            </div>
            <!-- <div class="relative">
                <input type="text" placeholder="Cari modul/konten" class="bg-gray-700 text-white rounded-full py-1 px-4 pl-10 w-64 focus:outline-none focus:ring-2 focus:ring-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
                <span class="absolute right-3 top-1.5 text-xs text-gray-400">CTRL /</span>
            </div>
            <div>
                <button class="p-1 rounded-full hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div> -->
        </div>
    </header>

