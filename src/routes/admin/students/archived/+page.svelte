<script lang="ts">
  import { onMount } from "svelte";
  import { writable, get } from "svelte/store";
  import Modal from "../../modals/view_student.svelte";
  import { apiUrl } from '$lib/api_base';

  // Define writable stores
  let selectedGender = writable("All");
  let searchQuery = writable("");
  let archivedStudents = writable([]);
  let filteredStudents = writable([]);
  let sortKey = writable("studentName");
  let sortOrder = writable("asc");
  let showModal = writable(false);
  let selectedPerson = writable(null);
  let isUnarchiving = false;

  // Gender filter options
  const genders = ["All", "Male", "Female"];

  // Table headers configuration
  const tableHeaders = [
    { key: "idNo", label: "Student ID" },
    { key: "studentName", label: "Name" },
    { key: "studentGender", label: "Gender" },
    { key: "studentLevel", label: "Level" },
    { key: "studentRibbon", label: "Ribbon" },
    { key: "studentColtrash", label: "Collected Trash" },
    { key: "actions", label: "Actions" }
  ];

  // Fetch archived student data on component mount
  onMount(async () => {
    await fetchArchivedStudents();
  });

  // Function to fetch archived students
  async function fetchArchivedStudents() {
    try {
      const response = await fetch(apiUrl('fetch_archived_students.php'));
      if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
      
      let data = await response.json();
      archivedStudents.set(data);
      filteredStudents.set(data);
    } catch (error) {
      console.error("❌ Error fetching archived students:", error);
      archivedStudents.set([]);
      filteredStudents.set([]);
    }
  }

  // Filter data based on search query and gender
  function filterData() {
    const query = get(searchQuery).toLowerCase();
    const gender = get(selectedGender);

    filteredStudents.set(
      get(archivedStudents).filter(student => {
        return (
          (gender === "All" || student.studentGender === gender) &&
          student.studentName.toLowerCase().includes(query)
        );
      })
    );
  }

  // Sort table by column
  function sortBy(key: string) {
    if (get(sortKey) === key) {
      sortOrder.update(order => (order === "asc" ? "desc" : "asc"));
    } else {
      sortKey.set(key);
      sortOrder.set("asc");
    }

    filteredStudents.update(items => {
      return [...items].sort((a, b) => {
        let valA = a[key];
        let valB = b[key];
        if (typeof valA === "string") {
          return valA.localeCompare(valB) * (get(sortOrder) === "asc" ? 1 : -1);
        }
        return (valA - valB) * (get(sortOrder) === "asc" ? 1 : -1);
      });
    });
  }

  // Unarchive student
  async function unarchiveStudent(student: any, event: Event) {
    event.stopPropagation(); // Prevent row click from opening modal
    
    if (!confirm(`Are you sure you want to restore ${student.studentName}?`)) {
      return;
    }

    isUnarchiving = true;
    try {
      const response = await fetch(apiUrl('archive_student.php'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          pk_studentID: student.pk_studentID,
          archive: 0 // Unarchive
        })
      });

      const result = await response.json();

      if (result.success) {
        // Remove from archived list
        archivedStudents.update(students => 
          students.filter(s => s.pk_studentID !== student.pk_studentID)
        );
        filteredStudents.update(students => 
          students.filter(s => s.pk_studentID !== student.pk_studentID)
        );
        alert(`${student.studentName} has been restored successfully!`);
      } else {
        alert(`Failed to restore student: ${result.message}`);
      }
    } catch (error) {
      console.error('Error unarchiving student:', error);
      alert('Failed to restore student. Please try again.');
    } finally {
      isUnarchiving = false;
    }
  }

  // Open modal with selected student
  function openModal(person) {
    selectedPerson.set(person);
    showModal.set(true);
  }

  // Close modal
  function closeModal() {
    showModal.set(false);
    selectedPerson.set(null);
  }
</script>

<div class="text-gray-500 font-bold text-2xl pl-10">
  <h1>Archived Students</h1>
</div>

<div class="p-6 max-w-6xl mx-auto bg-white rounded-xl shadow-lg">
  <div class="flex items-center gap-4 mb-6">
    <!-- Search input -->
    <input 
      type="text" 
      bind:value={$searchQuery} 
      on:input={filterData} 
      placeholder="Search by name..." 
      class="w-80 p-2 border border-gray-300 rounded-md text-gray-700 shadow-sm focus:ring-2 focus:ring-lime-500" 
    />
    
    <!-- Gender filter -->
    <select 
      bind:value={$selectedGender} 
      on:change={filterData}
      class="p-2 w-22 border border-gray-300 rounded-md text-gray-700 shadow-sm focus:ring-2 focus:ring-lime-500"
    >
      {#each genders as gender}
        <option value={gender}>{gender}</option>
      {/each}
    </select>
    
    <!-- Back button -->
    <a 
      href="/admin/students" 
      class="ml-auto bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600 shadow"
    >
      ← Back to Students
    </a>
  </div>

  {#if $filteredStudents.length === 0}
    <div class="text-center py-12 text-gray-500">
      <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
      </svg>
      <p class="text-lg font-semibold">No archived students found</p>
      <p class="text-sm mt-2">All archived students will appear here</p>
    </div>
  {:else}
    <!-- Table -->
    <div class="max-h-[75vh] overflow-y-auto w-full">
      <table class="w-full border-collapse bg-white rounded-lg text-sm">
        <thead class="sticky top-0 z-10 bg-gray-500 text-white">
          <tr>
            {#each tableHeaders as header}
              <th 
                class="p-2 cursor-pointer hover:bg-gray-400 transition" 
                on:click={() => header.key !== 'actions' && sortBy(header.key)}
              >
                {header.label}
                {#if header.key !== 'actions'}
                  <span class="ml-1 text-xs">
                    {($sortKey === header.key && $sortOrder === "asc") ? "▲" : "▼"}
                  </span>
                {/if}
              </th>
            {/each}
          </tr>
        </thead>
        <tbody class="divide-y">
          {#each $filteredStudents as student}
            <tr 
              class="border border-black/10 border-x-0 bg-gray-50 hover:bg-gray-200 transition" 
              on:click={() => openModal(student)}
            >
              <td class="p-2 text-center font-mono text-sm">{student.idNo}</td>
              <td class="p-2">{student.studentName}</td>
              <td class="p-2 flex justify-center items-center">
                {#if student.studentGender === 'Male'}
                  <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2h8v8h-2V5.414l-4.293 4.293a7 7 0 1 1-1.414-1.414L18.586 4H14V2ZM5 11a5 5 0 1 0 10 0 5 5 0 0 0-10 0Z"/>
                  </svg>
                {:else if student.studentGender === 'Female'}
                  <svg class="w-5 h-5 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a7 7 0 1 1-1 13.93V18h2v-2.07A7 7 0 0 1 12 2Zm0 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-1 6v-2h2v2h3v2H8v-2h3Z"/>
                  </svg>
                {/if}
              </td>
              <td class="p-2 text-center">{student.studentLevel}</td>
              <td class="p-2 text-center">{student.studentRibbon}</td>
              <td class="p-2 text-center">{student.studentColtrash}</td>
              <td class="p-2 text-center">
                <button 
                  on:click={(e) => unarchiveStudent(student, e)}
                  disabled={isUnarchiving}
                  class="bg-lime-500 text-white px-4 py-1 rounded hover:bg-lime-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  {isUnarchiving ? 'Restoring...' : 'Restore'}
                </button>
              </td>
            </tr>
          {/each}
        </tbody>
      </table>
    </div>
  {/if}
</div>

<!-- Modal for viewing student details -->
{#if $showModal}
  <Modal selectedPerson={$selectedPerson} on:close={closeModal} isArchived={true} />
{/if}
