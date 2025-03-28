<!-- src/routes/login.svelte -->
<script lang="ts">
  import { goto } from '$app/navigation';
  import LoginAuth from './student/loginauth.svelte';

  let userType = "student"; // Default to Student

  // Student credentials
  let studentId = '';
  let studentPassword = '';
  let showStudentPassword = false; // Visibility toggle

  // Teacher credentials (placeholder for now)
  let teacherId = '';
  let teacherPassword = '';
  let showTeacherPassword = false; // Visibility toggle

  let errorMessage = '';

  // Reference to LoginAuth component
  let loginAuth;

  function handleLogin(event) {
    event.preventDefault();
    if (userType === 'student') {
      loginAuth.authenticate(); // Trigger authentication
    } else if (userType === 'teacher') {
      // Placeholder for teacher login (unchanged for now)
      if (teacherId === '54321' && teacherPassword === '54321') {
        goto('/admin');
      } else {
        errorMessage = 'Invalid credentials. Try again.';
      }
    }
    }
  
    // Update errorMessage from LoginAuth
    function updateError(event) {
      errorMessage = event.detail;
    }
  </script>

  <div class="flex items-center justify-center min-h-screen bg-gray-100"
       style="background: url('/src/assets/readville.jpg') no-repeat center center/cover;">
    <div class="w-full max-w-sm p-6 bg-white/50 rounded-lg shadow-md relative overflow-hidden backdrop-blur-lg">
      <!-- Toggle Bar -->
      <div class="relative flex bg-gray-100 rounded-full p-1 mb-4">
        <button 
          class="w-1/2 py-2 text-sm font-medium relative z-10 transition-all duration-300 rounded-full"
          class:text-white={userType === "student"}
          class:text-gray-700={userType !== "student"}
          class:bg-orange-500={userType === "student"}
          on:click={() => userType = "student"}
        >
          STUDENT
        </button>
        <button 
          class="w-1/2 py-2 text-sm font-medium relative z-10 transition-all duration-300 rounded-full"
          class:text-white={userType === "teacher"}
          class:text-gray-700={userType !== "teacher"}
          class:bg-lime-600={userType === "teacher"}
          on:click={() => userType = "teacher"}
        >
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
          <form class="mt-4" on:submit={handleLogin}>
            <div>
              <label class="block text-sm font-medium text-gray-600">Student ID</label>
              <input 
                type="text" 
                bind:value={studentId} 
                required 
                class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:ring-blue-300" 
              />
            </div>
            <div class="mt-4 relative">
              <label class="block text-sm font-medium text-gray-600">Password</label>
              <input 
                type={showStudentPassword ? "text" : "password"} 
                bind:value={studentPassword} 
                required 
                class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:ring-blue-300 pr-10" 
              />
              <button 
                type="button" 
                class="absolute top-9 right-3 text-gray-600"
                on:click={() => showStudentPassword = !showStudentPassword}
              >
                {showStudentPassword ? '👁️' : '👁️‍🗨️'}
              </button>
            </div>
            <button 
              type="submit" 
              class="w-full px-4 py-2 mt-4 text-white bg-orange-500 rounded-md hover:bg-orange-600"
            >
              Login
            </button>
          </form>
        </div>

        <!-- Teacher Login Form (unchanged for now) -->
        <div class="absolute inset-0 transition-transform duration-500"
             style="transform: translateX({userType === 'teacher' ? '0%' : '110%'})">
          <h2 class="text-2xl font-semibold text-center text-gray-700">Teacher Login</h2>
          {#if errorMessage && userType === "teacher"}
            <p class="mt-2 text-sm text-red-500">{errorMessage}</p>
            {/if}
            <form class="mt-4" on:submit={handleLogin}>
              <div>
                <label class="block text-sm font-medium text-gray-600">Teacher ID</label>
                <input 
                  type="text" 
                  bind:value={teacherId} 
                  required 
                  class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:ring-lime-300" 
                />
              </div>
              <div class="mt-4 relative">
                <label class="block text-sm font-medium text-gray-600">Password</label>
                <input 
                  type={showTeacherPassword ? "text" : "password"} 
                  bind:value={teacherPassword} 
                  required 
                  class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:ring-lime-300 pr-10" 
                />
                <button 
                  type="button" 
                  class="absolute top-9 right-3 text-gray-600"
                  on:click={() => showTeacherPassword = !showTeacherPassword}
                >
                  {showTeacherPassword ? '👁️' : '👁️‍🗨️'}
                </button>
              </div>
              <button 
                type="submit" 
                class="w-full px-4 py-2 mt-4 text-white bg-lime-600 rounded-md hover:bg-lime-800"
              >
                Login
              </button>
            </form>
          </div>
        </div>
      </div>
         <!-- Hidden LoginAuth component for student authentication -->
    {#if userType === 'student'}
    <LoginAuth 
      bind:this={loginAuth} 
      idNo={studentId} 
      password={studentPassword} 
      on:error={updateError} 
    />
  {/if}
</div>