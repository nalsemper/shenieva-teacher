<script lang="ts">
    import { createEventDispatcher } from "svelte";
    import { studentData } from "$lib/store/store_students";
  
    const dispatch = createEventDispatcher();
  
    let studentName = "";
    let studentGender = "Male";
    let studentLevel = "";
    let studentRibbon = "";
    let studentColtrash = 0;
    let isSubmitting = false;
    let errorMessage = "";
  
    async function addStudent() {
      isSubmitting = true;
      errorMessage = "";
  
      const newStudent = {
        studentName,
        studentGender,
        studentLevel,
        studentRibbon,
        studentColtrash,
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
        <label class="block text-sm font-medium">Name</label>
        <input type="text" bind:value={studentName} class="w-full p-2 border rounded" required />
      </div>
  
      <div class="mb-3">
        <label class="block text-sm font-medium">Gender</label>
        <select bind:value={studentGender} class="w-full p-2 border rounded">
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>
  
      <div class="mb-3">
        <label class="block text-sm font-medium">Level</label>
        <input type="text" bind:value={studentLevel} class="w-full p-2 border rounded" required />
      </div>
  
      <div class="mb-3">
        <label class="block text-sm font-medium">Ribbon</label>
        <input type="text" bind:value={studentRibbon} class="w-full p-2 border rounded" required />
      </div>
  
      <div class="mb-3">
        <label class="block text-sm font-medium">Collected Trash</label>
        <input type="number" bind:value={studentColtrash} class="w-full p-2 border rounded" min="0" required />
      </div>
  
      <div class="flex justify-end gap-3">
        <button on:click={() => dispatch("close")} class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
        <button on:click={addStudent} class="px-4 py-2 bg-lime-500 text-white rounded" disabled={isSubmitting}>
          {isSubmitting ? "Adding..." : "Add"}
        </button>
      </div>
    </div>
  </div>
  