<script>
    import { goto } from '$app/navigation';

    let userType = "student"; // Default to Student

    // Student credentials
    let studentId = '';
    let studentPassword = '';
    let showStudentPassword = false; // Visibility toggle

    // Teacher credentials
    let teacherId = '';
    let teacherPassword = '';
    let showTeacherPassword = false; // Visibility toggle

    let errorMessage = '';

    function handleLogin() {
        if (userType === 'student' && studentId === '12345' && studentPassword === '12345') {
            goto('/student'); // Redirect to student dashboard
        } else if (userType === 'teacher' && teacherId === '54321' && teacherPassword === '54321') {
            goto('/admin'); // Redirect to teacher dashboard
        } else {
            errorMessage = 'Invalid credentials. Try again.';
        }
    }
</script>

<div class="flex items-center justify-center min-h-screen bg-gray-100"
     style="background: url('src/assets/readville.jpg') no-repeat center center/cover;">
    <div class="w-full max-w-sm p-6 bg-white/50 rounded-lg shadow-md relative overflow-hidden backdrop-blur-lg">
        <!-- Toggle Bar -->
        <div class="relative flex bg-gray-100 rounded-full p-1 mb-4">
            <button 
                class="w-1/2 py-2 text-sm font-medium relative z-10 transition-all duration-300 rounded-full"
                class:text-white={userType === "student"}
                class:text-gray-700={userType !== "student"}
                class:bg-orange-500={userType === "student"}
                on:click={() => userType = "student"}>
                STUDENT
            </button>
        
            <button 
                class="w-1/2 py-2 text-sm font-medium relative z-10 transition-all duration-300 rounded-full"
                class:text-white={userType === "teacher"}
                class:text-gray-700={userType !== "teacher"}
                class:bg-lime-600={userType === "teacher"}
                on:click={() => userType = "teacher"}>
                TEACHER
            </button>
        </div>
        
        <div class="relative w-full h-70">
            <!-- Student Login Form -->
            <div class="absolute inset-0 transition-transform duration-500"
                 style="transform: translateX({userType === 'student' ? '0%' : '-110%'})">
                <h2 class="text-2xl font-semibold text-center text-gray-700">Student Login</h2>
                {#if errorMessage && userType === "student"}
                    <p class="mt-2 text-sm text-red-500">{errorMessage}</p>
                {/if}
                <form class="mt-4" on:submit|preventDefault={handleLogin}>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Student ID</label>
                        <input type="text" bind:value={studentId} required 
                            class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:ring-blue-300" />
                    </div>
                    <div class="mt-4 relative">
                        <label class="block text-sm font-medium text-gray-600">Password</label>
                        <input 
                            type={showStudentPassword ? "text" : "password"} 
                            bind:value={studentPassword} 
                            required 
                            class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 pr-10" />
                        <button 
                            type="button" 
                            class="absolute top-9 right-3 text-gray-600"
                            on:click={() => showStudentPassword = !showStudentPassword}>
                            
                        </button>
                    </div>
                    <button type="submit" 
                        class="w-full px-4 py-2 mt-4 text-white bg-orange-500 rounded-md hover:bg-orange-600">
                        Login
                    </button>
                </form>
            </div>

            <!-- Teacher Login Form -->
            <div class="absolute inset-0 transition-transform duration-500"
                 style="transform: translateX({userType === 'teacher' ? '0%' : '110%'})">
                <h2 class="text-2xl font-semibold text-center text-gray-700">Teacher Login</h2>
                {#if errorMessage && userType === "teacher"}
                    <p class="mt-2 text-sm text-red-500">{errorMessage}</p>
                {/if}
                <form class="mt-4" on:submit|preventDefault={handleLogin}>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Teacher ID</label>
                        <input type="text" bind:value={teacherId} required 
                            class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:ring-lime-300" />
                    </div>
                    <div class="mt-4 relative">
                        <label class="block text-sm font-medium text-gray-600">Password</label>
                        <input 
                            type={showTeacherPassword ? "text" : "password"} 
                            bind:value={teacherPassword} 
                            required 
                            class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:ring-lime-300 pr-10" />
                        <button 
                            type="button" 
                            class="absolute top-9 right-3 text-gray-600"
                            on:click={() => showTeacherPassword = !showTeacherPassword}>
                        </button>
                    </div>
                    <button type="submit" 
                        class="w-full px-4 py-2 mt-4 text-white bg-lime-600 rounded-md hover:bg-lime-800">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
