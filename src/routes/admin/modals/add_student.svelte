<script lang="ts">
  import { createEventDispatcher } from "svelte";
  import { studentData } from "$lib/store/store_students";

  const dispatch = createEventDispatcher();

  let idNo = "";
  let studentName = "";
  let studentGender = "Male";
  let studentPass = "";
  let showPassword = false;
  let isSubmitting = false;
  let errorMessage = "";

  async function addStudent() {
    isSubmitting = true;
    errorMessage = "";

    const newStudent = {
      idNo, // ✅ Ensure ID is included
      studentName,
      studentGender,
      studentPass,
    };

    try {
      const response = await fetch("http://localhost/shenieva-teacher/src/lib/api/add_student.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(newStudent),
      });

      const result = await response.json();

      if (result.success) {
        studentData.update((students) => [...students, newStudent]);
        dispatch("close");
      } else {
        errorMessage = result.message;
      }
    } catch (error) {
      errorMessage = "Failed to connect to the server.";
    }

    isSubmitting = false;
  }
</script>

<div class="fixed inset-0 flex items-center justify-center bg-black/30 z-30">
  <div class="bg-white p-6 rounded-lg shadow-lg w-96">
    <h2 class="text-xl font-bold mb-4">Add New Student</h2>

    {#if errorMessage}
      <p class="text-red-500">{errorMessage}</p>
    {/if}

    <div class="mb-3">
      <label class="block text-sm font-medium">Student ID</label>
      <input type="text" bind:value={idNo} class="w-full p-2 border rounded" required />
    </div>

    <div class="mb-3">
      <label class="block text-sm font-medium">Name</label>
      <input type="text" bind:value={studentName} class="w-full p-2 border rounded" required />
    </div>

    <div class="mb-0 relative">
      <label class="block text-sm font-medium">Password</label>
      <input 
        type={showPassword ? "text" : "password"} 
        bind:value={studentPass} 
        class="w-full p-2 border rounded pr-10" 
        required 
      />
      
      <!-- Eye Icon -->
      <button 
        type="button" 
        class="absolute right-3 top-7 text-gray-500" 
        on:click={() => showPassword = !showPassword}
      >
        {showPassword ? "👁️" : "👁️‍🗨️"}
      </button>

    <div class="flex justify-end gap-3 mt-3">
      <button on:click={() => dispatch("close")} class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
      <button on:click={addStudent} class="px-4 py-2 bg-lime-500 text-white rounded" disabled={isSubmitting}>
        {isSubmitting ? "Adding..." : "Add"}
      </button>
    </div>
    </div>
  </div>
</div>
