<!-- src/routes/admin/modals/view_student.svelte -->
<script lang="ts">
  import { createEventDispatcher, onMount } from "svelte";
  import { writable } from "svelte/store";
  import EditStudentModal from "./edit_student.svelte"; // Import the new edit modal

  export let selectedPerson; // Student data passed from parent

  const dispatch = createEventDispatcher();

  interface AttendanceRecord {
    studentName: string;
    attendanceDateTime: string;
  }

  let attendanceData = writable<AttendanceRecord[]>([]);
  let showEditModal = false;
  let localPerson = { ...selectedPerson }; // Local copy to update after edit

  const tabs = [
    { id: "personal", label: "Personal Data" },
    { id: "attendance", label: "Attendance" },
    { id: "quiz1", label: "Quiz 1" },
    { id: "quiz2", label: "Quiz 2" },
    { id: "quiz3", label: "Quiz 3" },
  ];

  let activeTab = "personal";

  function handleClose() {
    dispatch("close");
  }

  function handleEdit() {
    showEditModal = true;
  }

  function handleEditSave(event: CustomEvent) {
    const updatedStudent = event.detail;
    // Update localPerson to refresh the UI
    localPerson = { ...localPerson, ...updatedStudent };
    // Pass the edit event up to the parent to update studentData
    dispatch("edit", updatedStudent);
    showEditModal = false;
  }

  function setActiveTab(tabId: string) {
    activeTab = tabId;
  }

  // Handle backdrop click or keyboard activation
  function handleBackdropClose(event: MouseEvent | KeyboardEvent) {
    if (event.type === "click" && event.target !== event.currentTarget) {
      return;
    }
    if (
      event.type === "keydown" &&
      !(
        event instanceof KeyboardEvent &&
        (event.key === "Enter" || event.key === " ")
      )
    ) {
      return;
    }
    dispatch("close");
  }

  onMount(() => {
    window.addEventListener("keydown", handleKeydown);
    return () => {
      window.removeEventListener("keydown", handleKeydown);
    };
  });

  function handleKeydown(event: KeyboardEvent) {
    if (event.key === "Escape") {
      dispatch("close");
    }
  }

  // Reactive statement for level progress
  $: levelProgress = Math.min(
    ((parseInt(localPerson.studentLevel) || 0) / 3) * 100,
    100,
  );

  async function fetchAttendanceData(studentID: string) {
    try {
      const response = await fetch(
        `http://localhost/shenieva-teacher/src/lib/api/fetch_attendance.php?studentID=${studentID}`,
      );
      if (!response.ok)
        throw new Error(`HTTP error! Status: ${response.status}`);
      const data = await response.json();
      if (data.error) {
        console.warn("🚨 Attendance fetch warning:", data.error);
        attendanceData.set([]); // Ensure it's still an array for the UI logic
      } else {
        attendanceData.set(data); // Successfully set fetched data
      }
    } catch (error) {
      console.error("❌ Error fetching attendance data:", error);
      attendanceData.set([]);
    }
  }

  onMount(() => {
    console.log("Selected Person ID:", selectedPerson.pk_studentID);
    if (selectedPerson?.pk_studentID) {
      fetchAttendanceData(selectedPerson.pk_studentID);
    }
  });

  let currentPage = 1;
  let pageSize = 10;

  // Computed data for pagination
  $: paginatedAttendance = $attendanceData.slice(
    (currentPage - 1) * pageSize,
    currentPage * pageSize,
  );

  // Pagination Controls
  function nextPage() {
    if (currentPage * pageSize < $attendanceData.length) {
      currentPage++;
    }
  }

  function prevPage() {
    if (currentPage > 1) {
      currentPage--;
    }
  }
</script>

<div
  class="fixed inset-0 bg-black/30 flex items-center justify-center z-30"
  on:click={handleBackdropClose}
  on:keydown={handleBackdropClose}
  role="button"
  tabindex="0"
  aria-label="Close modal by clicking outside"
>
  <!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
  <!-- svelte-ignore a11y_click_events_have_key_events -->
  <div
    class="bg-white rounded-lg shadow-lg w-full max-w-lg mx-4"
    role="dialog"
    aria-labelledby="modal-title"
    aria-modal="true"
    on:click|stopPropagation
  >
    <!-- Header -->
    <div class="flex justify-between items-center p-4 border-b border-gray-200">
      <h2 id="modal-title" class="text-xl font-semibold text-gray-900">
        Student Details
      </h2>
      <button
        type="button"
        class="text-gray-500 hover:text-gray-700 text-2xl font-medium leading-none focus:outline-none focus:ring-2 focus:ring-lime-500"
        on:click={handleClose}
        aria-label="Close modal"
      >
        ×
      </button>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
      <nav class="flex space-x-1 px-4">
        {#each tabs as tab}
          <button
            type="button"
            class="py-2 px-4 text-sm font-medium {activeTab === tab.id
              ? 'text-lime-600 border-b-2 border-lime-500'
              : 'text-gray-500 hover:text-gray-700'} focus:outline-none focus:ring-2 focus:ring-lime-500"
            on:click={() => setActiveTab(tab.id)}
          >
            {tab.label}
          </button>
        {/each}
      </nav>
    </div>

    <!-- Content -->
    <div class="px-4 py-2">
      {#if activeTab === "personal"}
        <!-- Personal Data -->
        <div class="space-y-3">
          <div class="flex items-center gap-2">
            <span class="text-gray-600 font-medium min-w-[120px]">ID:</span>
            <span class="text-gray-900">{localPerson.idNo || "N/A"}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-gray-600 font-medium min-w-[120px]">Name:</span>
            <span class="text-gray-900">{localPerson.studentName}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-gray-600 font-medium min-w-[120px]">Gender:</span>
            <span class="text-gray-900">{localPerson.studentGender}</span>
          </div>
        </div>

        <!-- Performance Overview -->
        <div class="space-y-4 mt-4">
          <h3 class="text-lg font-medium text-gray-800">
            Performance Overview
          </h3>
          <div>
            <span class="text-gray-600 font-medium block mb-1">Level:</span>
            <div class="w-full bg-gray-200 rounded-full h-4">
              <div
                class="bg-lime-500 h-4 rounded-full"
                style="width: {levelProgress}%"
                aria-label={`Level: ${localPerson.studentLevel} out of 3`}
              ></div>
            </div>
            <span class="text-gray-700 text-sm mt-1 block">
              {localPerson.studentLevel} / 3
            </span>
          </div>
          <div>
            <span class="text-gray-600 font-medium block mb-1">Ribbons:</span>
            <div class="flex items-center gap-2">
              <div
                class="bg-orange-500 text-white w-8 h-8 flex items-center justify-center rounded-full font-semibold"
              >
                {localPerson.studentRibbon}
              </div>
              <span class="text-gray-900">Ribbons Earned</span>
            </div>
          </div>
          <div>
            <span class="text-gray-600 font-medium block mb-1"
              >Collected Trash:</span
            >
            <div class="flex items-center gap-2">
              <div
                class="bg-lime-500 text-white w-8 h-8 flex items-center justify-center rounded-full font-semibold"
              >
                {localPerson.studentColtrash}
              </div>
              <span class="text-gray-900">Trash Collected</span>
            </div>
          </div>
        </div>
      {:else if activeTab === "attendance"}
        <div class="text-gray-700">
          <h3 class="text-lg font-medium text-gray-800 mb-2">
            Attendance Records
          </h3>
          {#if paginatedAttendance.length > 0}
            <ul>
              {#each paginatedAttendance as record, index}
                <li
                  class="flex justify-between border-b-[0.2px] border-gray-300 py-2 px-4"
                >
                  <span class="text-sm font-semibold"
                    >{(currentPage - 1) * pageSize + index + 1}. {record.studentName}</span
                  >
                  <span class="text-gray-500 text-sm font-semibold"
                    >{record.attendanceDateTime}</span
                  >
                </li>
              {/each}
            </ul>
          {:else}
            <p class="text-gray-500">No attendance records found.</p>
          {/if}
        </div>
      {:else if activeTab === "quiz1"}
        <div class="text-gray-700">
          Quiz 1 results for {localPerson.studentName} will be displayed here.
        </div>
      {:else if activeTab === "quiz2"}
        <div class="text-gray-700">
          Quiz 2 results for {localPerson.studentName} will be displayed here.
        </div>
      {:else if activeTab === "quiz3"}
        <div class="text-gray-700">
          Quiz 3 results for {localPerson.studentName} will be displayed here.
        </div>
      {/if}
    </div>

    <!-- Footer -->
    <div
      class="p-4 border-t border-gray-200 flex justify-end items-center gap-4"
    >
      <!-- Pagination Buttons - Show only for Attendance Tab -->
      {#if activeTab === "attendance" && $attendanceData.length > 10}
        <div class="flex gap-2">
          <button
            class="px-4 py-2 bg-gray-300 rounded-md hover:bg-lime-500"
            on:click={prevPage}
            disabled={currentPage === 1}
          >
            &lt;
          </button>

          <button
            class="px-4 py-2 bg-gray-300 rounded-md hover:bg-lime-500"
            on:click={nextPage}
            disabled={currentPage * pageSize >= $attendanceData.length}
          >
            &gt;
          </button>
        </div>
      {/if}

      <!-- Close Button -->
      <button
        type="button"
        on:click={handleClose}
        class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2"
      >
        Close
      </button>
    </div>
  </div>

  {#if showEditModal}
    <EditStudentModal
      selectedPerson={localPerson}
      on:close={() => (showEditModal = false)}
      on:edit={handleEditSave}
    />
  {/if}
</div>
