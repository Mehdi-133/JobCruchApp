<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouConnect - Create New Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

    <div class="max-w-3xl mx-auto py-12 px-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <header class="mb-8">
                <h1 class="text-2xl font-bold text-blue-700">Share Knowledge 💡</h1>
                <p class="text-gray-500">Contribute to the YouCode ecosystem across Nador, Safi, and Youssoufia.</p>
            </header>

            <form action="/dashboard" method="POST" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Content Type</label>
                        <select name="post_type" class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                            <option value="question">Question ❓</option>
                            <option value="blog">Technical Blog ✍️</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Your Campus</label>
                        <select name="campus" class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                            <option value="nador">Nador</option>
                            <option value="safi">Safi</option>
                            <option value="youssoufia">Youssoufia</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Headline</label>
                    <input type="text" name="title" placeholder="e.g., Optimizing Eloquent queries in Laravel" 
                           class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Details</label>
                    <textarea name="content" rows="8" placeholder="Share your insights or describe your technical challenge..." 
                              class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-all active:scale-95">
                        Publish to YouConnect 🚀
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-4 italic">
                        *AI Content Validation will be performed upon submission.
                    </p>
                </div>
            </form>
        </div>
    </div>

</body>
</html>