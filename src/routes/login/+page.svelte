<!-- src/routes/login/+page.svelte -->
<script lang="ts">
  import { goto } from '$app/navigation';

  let userType = "student";
  let studentId = '';
  let studentPassword = '';
  let showStudentPassword = false;
  let teacherId = '';
  let teacherPassword = '';
  let showTeacherPassword = false;
  let errorMessage = '';

  async function handleLogin(event: SubmitEvent) {
    event.preventDefault();
    
    const formData = new FormData();
    formData.append('userType', userType);

    if (userType === 'student') {
      formData.append('idNo', studentId);
      formData.append('password', studentPassword);
    } else if (userType === 'teacher') {
      formData.append('idNo', teacherId);
      formData.append('password', teacherPassword);
    }

    try {
      console.log('Sending form data:', Object.fromEntries(formData));
      const response = await fetch('/login', {
        method: 'POST',
        body: formData
      });
      
      console.log('Response status:', response.status);
      const result = await response.json();
      console.log('Response body:', result);

      
      let actionData = result.data;
      if (typeof actionData === 'string') {
        actionData = JSON.parse(actionData); 
      }

      
      let loginResult;
      if (Array.isArray(actionData)) {
       
        loginResult = {
          success: actionData[1],
          userType: actionData[2],
          teacherId: actionData[3],
          teacherName: actionData[4] 
        };
      } else {
        loginResult = actionData; 
      }

      console.log('Parsed login result:', loginResult); 

      if (response.ok && loginResult.success) {
        if (userType === 'student') {
          goto('/student');
        } else if (userType === 'teacher') {
          goto('/admin');
        }
      } else {
        errorMessage = loginResult.error || result.error || 'Invalid credentials';
      }
    } catch (err) {
      errorMessage = 'An error occurred during login';
      console.error('Client-side login error:', err);
    }
  }

  function updateError(event: CustomEvent<string>) {
    errorMessage = event.detail;
  }
</script>

<div class="flex items-center justify-center min-h-screen bg-gray-100"
     style="background: url('/src/assets/readville.jpg') no-repeat center center/cover;">
  <div class="w-full max-w-sm p-6 bg-white/50 rounded-lg shadow-md relative overflow-hidden backdrop-blur-lg">
  

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
            <label for="student-id" class="block text-sm font-medium text-gray-600">Student ID</label>
            <input 
              id="student-id"
              type="text" 
              bind:value={studentId} 
              required 
              class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:ring-blue-300" 
            />
          </div>
          <div class="mt-4 relative">
            <label for="student-password" class="block text-sm font-medium text-gray-600">Password</label>
            <input 
              id="student-password"
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

      <!-- Teacher Login Form -->
      <div class="absolute inset-0 transition-transform duration-500"
           style="transform: translateX({userType === 'teacher' ? '0%' : '110%'})">
        <h2 class="text-2xl font-semibold text-center text-gray-700">Teacher Login</h2>
        {#if errorMessage && userType === "teacher"}
          <p class="mt-2 text-sm text-red-500">{errorMessage}</p>
        {/if}
        <form class="mt-4" on:submit={handleLogin}>
          <div>
            <label for="teacher-id" class="block text-sm font-medium text-gray-600">Teacher ID</label>
            <input 
              id="teacher-id"
              type="text" 
              bind:value={teacherId} 
              required 
              class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:ring-lime-300" 
            />
          </div>
          <div class="mt-4 relative">
            <label for="teacher-password" class="block text-sm font-medium text-gray-600">Password</label>
            <input 
              id="teacher-password"
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
</div>