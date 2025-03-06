<!-- src/routes/admin/modals/edit_student.svelte -->
<script lang="ts">
    import { createEventDispatcher, onMount } from 'svelte';
    import { studentData } from '$lib/store/store_students';
  
    export let selectedPerson; // Student data passed from parent
  
    const dispatch = createEventDispatcher();
  
    let isEditing = true; // Always in edit mode since this is an edit modal
    let editedPerson = { ...selectedPerson }; // Clone for editable fields
    let isSubmitting = false;
    let errorMessage = '';
  
    async function handleSave() {
      isSubmitting = true;
      errorMessage = '';
  
      const updatedStudent = {
        studentId: editedPerson.studentId,
        studentName: editedPerson.studentName,
        studentGender: editedPerson.studentGender,
      };
  
      try {
        const response = await fetch('http://localhost/shenieva-teacher/src/lib/api/update_student.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(updatedStudent),
        });
  
        const result = await response.json();
  
        if (result.success) {
          // Update the studentData store
          studentData.update(students =>
            students.map(s => s.studentId === updatedStudent.studentId ? updatedStudent : s)
          );
          dispatch('close');
        } else {
          errorMessage = result.message || 'Failed to update student';
        }
      } catch (error) {
        errorMessage = 'Failed to connect to the server.';
      }
  
      isSubmitting = false;
    }
  
    function handleCancel() {
      dispatch('close');
    }
  
    // Close modal with Escape key
    function handleKeydown(event: KeyboardEvent) {
      if (event.key === 'Escape') {
        handleCancel();
      }
    }
  
    onMount(() => {
      window.addEventListener('keydown', handleKeydown);
      return () => {
        window.removeEventListener('keydown', handleKeydown);
      };
    });
  </script>
  
  <div 
    class="fixed inset-0 bg-black/30 flex items-center justify-center z-30" 
    on:click|stopPropagation
  >
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
      <h2 class="text-xl font-bold mb-4">Edit Student</h2>
  
      {#if errorMessage}
        <p class="text-red-500 mb-4">{errorMessage}</p>
      {/if}
  
      <div class="space-y-3">
        <div>
          <label class="block text-sm font-medium text-gray-600">Student ID</label>
          <input 
            type="text" 
            bind:value={editedPerson.studentId} 
            class="w-full p-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-lime-500" 
            required 
          />
        </div>
  
        <div>
          <label class="block text-sm font-medium text-gray-600">Name</label>
          <input 
            type="text" 
            bind:value={editedPerson.studentName} 
            class="w-full p-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-lime-500" 
            required 
          />
        </div>
  
        <div>
          <label class="block text-sm font-medium text-gray-600">Gender</label>
          <select 
            bind:value={editedPerson.studentGender} 
            class="w-full p-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-lime-500"
          >
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>
      </div>
  
      <div class="flex justify-end gap-3 mt-6">
        <button 
          on:click={handleCancel} 
          class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400"
        >
          Cancel
        </button>
        <button 
          on:click={handleSave} 
          class="px-4 py-2 bg-lime-500 text-white rounded-md hover:bg-lime-600 focus:outline-none focus:ring-2 focus:ring-lime-400"
          disabled={isSubmitting}
        >
          {isSubmitting ? 'Saving...' : 'Save'}
        </button>
      </div>
    </div>
  </div>