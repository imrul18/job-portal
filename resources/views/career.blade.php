<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-200 min-h-screen">

    <div class="flex items-center justify-center mt-4">

        <div class="w-full max-w-4xl p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
            <h1 class="text-5xl font-semibold text-center mb-6">{{ $job->company }}</h1>
            <h3 class="text-3xl font-semibold text-center mb-6">{{ $job->name }}</h3>

            <!-- Display Validation Errors -->
            @if ($errors->any())
                <div class="bg-red-500
                text-white px-4 py-3 rounded-md shadow-md mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form id="uploadForm" action="{{ route('applications.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="w-full lg:w-1/2">
                        <input type="hidden" name="slug" value="{{ old('slug', $job->slug) }}">

                        <div class="mb-4">
                            <label for="name"
                                class="block font-medium text-gray-700 dark:text-gray-300">Name:</label>
                            <input type="text" id="name" name="name" placeholder="Enter your name"
                                value="{{ old('name') }}"
                                class="w-full px-4 py-2 mt-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email"
                                class="block font-medium text-gray-700 dark:text-gray-300">Email:</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email"
                                value="{{ old('email') }}"
                                class="w-full px-4 py-2 mt-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="block font-medium text-gray-700 dark:text-gray-300">Phone
                                Number:</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number"
                                value="{{ old('phone') }}"
                                class="w-full px-4 py-2 mt-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Custom File Input -->
                        <div class="mb-4">
                            <label class="block font-medium text-gray-700 dark:text-gray-300">Upload Your CV:</label>
                            <div
                                class="relative flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-400 rounded-md bg-gray-50 dark:bg-gray-700 cursor-pointer">
                                <input type="file" name="file" id="fileInput" accept=".pdf"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15V3m0 0l-4 4m4-4l4 4M4 20h16"></path>
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Click to upload or drag and
                                    drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Only PDF files allowed</p>
                                <p id="fileName" class="text-sm text-green-600 mt-2 hidden"></p>
                            </div>
                            @error('file')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                    <div class="w-full lg:w-1/2" id="pdfPreviewContainer">
                        <iframe id="pdfPreview" class="w-full h-[450px] border rounded-md shadow-md"></iframe>
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 transition-all text-white py-2 px-4 rounded-md shadow-md text-lg font-semibold mt-4">
                    Submit
                </button>
            </form>

        </div>
    </div>

    <div id="alertContainer" class="w-full max-w-4xl mx-auto mt-4">
        @if (session('success'))
            <div class="bg-green-500 text-white px-4 py-3 rounded-md shadow-md flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.style.display='none'" class="text-white font-bold">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-500 text-white px-4 py-3 rounded-md shadow-md flex justify-between items-center">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.style.display='none'" class="text-white font-bold">&times;</button>
            </div>
        @endif
    </div>

    <script>
        document.getElementById("fileInput").addEventListener("change", function(event) {
            const file = event.target.files[0];
            const fileNameDisplay = document.getElementById("fileName");
            const pdfPreviewContainer = document.getElementById("pdfPreviewContainer");
            const pdfPreview = document.getElementById("pdfPreview");

            if (file) {
                fileNameDisplay.textContent = `Selected File: ${file.name}`;
                // fileNameDisplay.classList.remove("hidden");

                if (file.type === "application/pdf") {
                    const fileURL = URL.createObjectURL(file);
                    pdfPreview.src = fileURL;
                    pdfPreviewContainer.classList.remove("hidden");
                } else {
                    pdfPreviewContainer.classList.add("hidden");
                }
            } else {
                fileNameDisplay.classList.add("hidden");
                pdfPreviewContainer.classList.add("hidden");
            }
        });

        setTimeout(() => {
            const alertBoxes = document.querySelectorAll("#alertContainer div");
            alertBoxes.forEach(alert => {
                alert.style.transition = "opacity 0.5s";
                alert.style.opacity = "0";
                setTimeout(() => alert.style.display = "none", 500);
            });
        }, 5000);
    </script>

</body>

</html>
