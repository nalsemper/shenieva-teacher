<script>
    import { onMount } from "svelte";
    import { writable, get } from "svelte/store";
    // import { writable } from "svelte/store";
    import Modal from "../modals/quiz3.svelte";
  
    let selectedGender = "All";
    let date = new Date().toISOString().split("T")[0]; // Default to today's date
    let attendees = writable([]);
    let sortKey = writable("name");
    let sortOrder = writable("asc");
    let showModal = writable(false);
    let selectedPerson = writable(null);
    
    const genders = ["All", "Male", "Female"];
  
    const tableHeaders = [
      { key: "name", label: "Name" },
      { key: "gender", label: "Gender" },
      { key: "datetime", label: "Date & Time" },
      { key: "score", label: "Score" },
      { key: "attempts", label: "Attempts" },
      { key: "status", label: "Status" },
    ];
  
    const data = [
  { name: "Alice Gou", gender: "Female", datetime: "2025-03-02 14:30", score: 85, attempts: 3, status: "Reviewed" },
  { name: "Bob Ackerman", gender: "Male", datetime: "2025-03-02 15:00", score: 78, attempts: 2, status: "Pending for Review" },
  { name: "Charlie Johnson", gender: "Male", datetime: "2025-03-02 15:45", score: 70, attempts: 1, status: "Reviewed" },
  { name: "David Kelly", gender: "Male", datetime: "2025-03-02 16:15", score: 65, attempts: 2, status: "Pending for Review" },
  { name: "Eve Nighcast", gender: "Female", datetime: "2025-03-02 17:00", score: 35, attempts: 4, status: "Reviewed" },
  { name: "Frank Mendez", gender: "Male", datetime: "2025-03-02 18:00", score: 77, attempts: 3, status: "Pending for Review" },
  { name: "Grace Hopper", gender: "Female", datetime: "2025-03-02 19:15", score: 82, attempts: 2, status: "Reviewed" },
  { name: "Hannah Lee", gender: "Female", datetime: "2025-03-02 20:30", score: 72, attempts: 3, status: "Pending for Review" },
  { name: "Ian McArthur", gender: "Male", datetime: "2025-03-02 21:45", score: 88, attempts: 1, status: "Reviewed" },
  { name: "Jackie Chan", gender: "Male", datetime: "2025-03-02 22:00", score: 50, attempts: 4, status: "Pending for Review" },
  { name: "Kylie Jenner", gender: "Female", datetime: "2025-03-02 23:15", score: 85, attempts: 2, status: "Reviewed" },
  { name: "Leo Messi", gender: "Male", datetime: "2025-03-02 10:45", score: 60, attempts: 3, status: "Pending for Review" },
  { name: "Mona Lisa", gender: "Female", datetime: "2025-03-02 11:30", score: 85, attempts: 2, status: "Reviewed" },
  { name: "Nathan Drake", gender: "Male", datetime: "2025-03-02 12:15", score: 55, attempts: 4, status: "Pending for Review" },
  { name: "Olivia Wilde", gender: "Female", datetime: "2025-03-02 13:30", score: 92, attempts: 1, status: "Reviewed" },
  { name: "Paul Newman", gender: "Male", datetime: "2025-03-02 14:45", score: 83, attempts: 3, status: "Pending for Review" },
  { name: "Quincy Adams", gender: "Male", datetime: "2025-03-02 16:00", score: 55, attempts: 2, status: "Reviewed" },
  { name: "Rachel Green", gender: "Female", datetime: "2025-03-02 17:15", score: 80, attempts: 2, status: "Pending for Review" },
  { name: "Samuel Jackson", gender: "Male", datetime: "2025-03-02 18:30", score: 48, attempts: 3, status: "Reviewed" },
  { name: "Tina Fey", gender: "Female", datetime: "2025-03-02 19:45", score: 88, attempts: 1, status: "Pending for Review" },
  { name: "Ursula K. Le Guin", gender: "Female", datetime: "2025-03-02 21:00", score: 65, attempts: 3, status: "Reviewed" },
  { name: "Victor Hugo", gender: "Male", datetime: "2025-03-02 22:15", score: 80, attempts: 2, status: "Pending for Review" },
  { name: "Wanda Maximoff", gender: "Female", datetime: "2025-03-02 23:30", score: 90, attempts: 1, status: "Reviewed" },
  { name: "Xander Cage", gender: "Male", datetime: "2025-03-02 09:00", score: 65, attempts: 4, status: "Pending for Review" }
];



  
    function filterData() {
      attendees.set(
        // @ts-ignore
        data.filter(person => {
          let personDate = person.datetime.split(" ")[0]; // Extract only the date part
          return (selectedGender === "All" || person.gender === selectedGender); // Filter by selected date
        })
      );
    }
  
    /**
   * @param {string} key
   */
    function sortBy(key) {
      sortKey.set(key);
      sortOrder.update(o => (o === "asc" ? "desc" : "asc"));
  
      attendees.update(items => {
        return [...items].sort((a, b) => {
          /**
           * @type {string | number}
           */
          let valA = a[key];
          /**
           * @type {number}
           */
          let valB = b[key];
  
          if (typeof valA === "string") {
            // @ts-ignore
            return valA.localeCompare(valB) * (get(sortOrder) === "asc" ? 1 : -1);
          } else {
            return (valA - valB) * (get(sortOrder) === "asc" ? 1 : -1);
          }
        });
      });
    }


    function openModal(person) {
        selectedPerson.set(person);
        showModal.set(true);
    }

    function closeModal() {
        showModal.set(false);
    }

  
    onMount(() => {
      filterData();
    });
  </script>
  
  <div class="p-6 max-w-6xl mx-auto bg-white rounded-xl shadow-lg">
    <div class="flex items-center gap-4 mb-6">
      <!-- Gender Filter -->
      <select bind:value={selectedGender} on:change={filterData}
        class="p-2 w-22 border border-gray-300 rounded-md text-gray-700 shadow-sm focus:ring-2 focus:ring-lime-500">
        {#each genders as gender}
          <option value={gender}>{gender}</option>
        {/each}
      </select>
  
      <!-- Date Filter -->
      <!-- <input type="date" bind:value={date} on:change={filterData}
        class="p-2 border border-gray-300 rounded-md text-gray-700 shadow-sm focus:ring-2 focus:ring-orange-500" /> -->
    </div>
  
    <!-- <div class="overflow-x-auto rounded-lg shadow-md"> -->
    <div class="max-h-[65vh] overflow-y-auto w-full">
      <table class="w-full border-collapse bg-white rounded-lg text-sm">
        <thead class="sticky top-0 z-10">
          <tr class="bg-lime-500 text-white">
            {#each tableHeaders as header}
              <th class="p-2 cursor-pointer hover:bg-lime-500 transition" on:click={() => sortBy(header.key)}>
                {header.label}
                <span class="ml-1 text-xs">{($sortKey === header.key && $sortOrder === "asc") ? "▲" : "▼"}</span>
              </th>
            {/each}
          </tr>
        </thead>
        <tbody>
          {#each $attendees as person}
            <tr class="border-b last:border-none bg-gray-50 hover:bg-orange-100 transition"  on:click={() => openModal(person)}>
              <td class="p-2">{person.name}</td>
              <!-- <td class="p-2">{person.gender}</td> -->
              <td class="p-2 flex items-center justify-center space-x-2">
                {#if person.gender === 'Male'}
                  <svg class="w-5 h-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2h8v8h-2V5.414l-4.293 4.293a7 7 0 1 1-1.414-1.414L18.586 4H14V2ZM5 11a5 5 0 1 0 10 0 5 5 0 0 0-10 0Z"/>
                  </svg>
                {/if}
                {#if person.gender === 'Female'}
                  <svg class="w-5 h-5 text-pink-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a7 7 0 1 1-1 13.93V18h2v-2.07A7 7 0 0 1 12 2Zm0 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-1 6v-2h2v2h3v2H8v-2h3Z"/>
                  </svg>
                {/if}
              </td>
                          
              <td class="p-2 text-center justify-center">{person.datetime}</td>
              <td class="p-2 text-center justify-center">{person.score}</td>
              <td class="p-2 text-center justify-center">{person.attempts}</td>
              <td class="p-2">
                <div class="w-full font-semibold rounded-xl h-4 relative flex items-center text-xs text-gray-700 z-0">
                  <span class="absolute w-full text-center text-blue-500">
                    {#if person.status == "Reviewed" } Reviewed {/if}
                  </span>
                  <span class="absolute w-full text-center text-red-500">
                    {#if person.status == "Pending for Review" } Pending {/if}
                  </span>
                </div>
              </td>
            </tr>
          {/each}
        </tbody>
      </table>
    </div>
  </div>

  {#if $showModal}
    <Modal selectedPerson={$selectedPerson} on:close={closeModal} />
{/if}


  
  