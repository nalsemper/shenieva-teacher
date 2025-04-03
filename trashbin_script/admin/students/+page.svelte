<script lang="ts">
  import { onMount } from "svelte";
  import { writable, get } from "svelte/store";
  // import { writable } from "svelte/store";
  import Modal from "../modals/quiz2.svelte";


  let selectedGender = "All";
  let date = new Date().toISOString().split("T")[0]; // Default to today's date
  let attendees = writable([]);
  let sortKey = writable("name");
  let sortOrder = writable("asc");
  let showModal = writable(false);
  let selectedPerson = writable(null);
  let searchQuery = writable("");
  
  

  const genders = ["All", "Male", "Female"];

  const tableHeaders = [
    { key: "name", label: "Name" },
    { key: "gender", label: "Gender" },
    { key: "level", label: "Level" },
    { key: "ribbon", label: "Ribbons" },
    { key: "collectedTrash", label: "Collected Trash" },
  ];

  const data = [
  { name: "Alice Gou", gender: "Male", datetime: "2025-03-03 14:30", level: 2, ribbon: 3, collectedTrash: 42 },
  { name: "Bob Ackerman", gender: "Female", datetime: "2025-03-03 15:00", level: 1, ribbon: 1, collectedTrash: 21 },
  { name: "Charlie Johnson", gender: "Female", datetime: "2025-03-03 15:45", level: 3, ribbon: 4, collectedTrash: 67 },
  { name: "David Kelly", gender: "Male", datetime: "2025-03-03 16:15", level: 2, ribbon: 2, collectedTrash: 53 },
  { name: "Eve Nighcast", gender: "Male", datetime: "2025-03-03 17:00", level: 1, ribbon: 0, collectedTrash: 10 },
  { name: "Frank Mendez", gender: "Female", datetime: "2025-03-03 18:00", level: 3, ribbon: 5, collectedTrash: 84 },
  { name: "Grace Hopper", gender: "Male", datetime: "2025-03-03 19:15", level: 2, ribbon: 2, collectedTrash: 45 },
  { name: "Hannah Lee", gender: "Female", datetime: "2025-03-03 20:30", level: 1, ribbon: 3, collectedTrash: 32 },
  { name: "Ian McArthur", gender: "Female", datetime: "2025-03-03 21:45", level: 3, ribbon: 4, collectedTrash: 78 },
  { name: "Jackie Chan", gender: "Female", datetime: "2025-03-03 22:00", level: 1, ribbon: 1, collectedTrash: 27 },
  { name: "Kylie Jenner", gender: "Male", datetime: "2025-03-03 23:15", level: 3, ribbon: 5, collectedTrash: 90 },
  { name: "Leo Messi", gender: "Female", datetime: "2025-03-03 10:45", level: 2, ribbon: 2, collectedTrash: 55 },
  { name: "Mona Lisa", gender: "Male", datetime: "2025-03-03 11:30", level: 3, ribbon: 3, collectedTrash: 74 },
  { name: "Nathan Drake", gender: "Female", datetime: "2025-03-03 12:15", level: 1, ribbon: 1, collectedTrash: 19 },
  { name: "Olivia Wilde", gender: "Male", datetime: "2025-03-03 13:30", level: 2, ribbon: 4, collectedTrash: 62 },
  { name: "Paul Newman", gender: "Female", datetime: "2025-03-03 14:45", level: 3, ribbon: 5, collectedTrash: 88 },
  { name: "Quincy Adams", gender: "Male", datetime: "2025-03-03 16:00", level: 1, ribbon: 0, collectedTrash: 7 },
  { name: "Rachel Green", gender: "Male", datetime: "2025-03-03 17:15", level: 2, ribbon: 2, collectedTrash: 38 },
  { name: "Samuel Jackson", gender: "Female", datetime: "2025-03-03 18:30", level: 3, ribbon: 3, collectedTrash: 60 },
  { name: "Tina Fey", gender: "Male", datetime: "2025-03-03 19:45", level: 1, ribbon: 2, collectedTrash: 33 },
  { name: "Ursula K. Le Guin", gender: "Male", datetime: "2025-03-03 21:00", level: 3, ribbon: 5, collectedTrash: 79 },
  { name: "Victor Hugo", gender: "Female", datetime: "2025-03-03 22:15", level: 2, ribbon: 1, collectedTrash: 20 },
  { name: "Wanda Maximoff", gender: "Male", datetime: "2025-03-03 23:30", level: 3, ribbon: 4, collectedTrash: 66 },
  { name: "Xander Cage", gender: "Female", datetime: "2025-03-03 09:00", level: 1, ribbon: 0, collectedTrash: 12 }
];



  function filterData() {
    attendees.set(
      // @ts-ignore
      data.filter(person => {
        let personDate = person.datetime.split(" ")[0]; // Extract only the date part
        return (selectedGender === "All" || person.gender === selectedGender)  &&
        person.name.toLowerCase().includes(get(searchQuery).toLowerCase()); // Filter by selected date
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

<div class=" text-gray-500 font-bold text-2xl pl-10">
  <h1>Students Management</h1>
</div>



<div class="p-6 max-w-6xl mx-auto bg-white rounded-xl shadow-lg">
  <div class="flex items-center gap-4 mb-6">
    <input 
    type="text" 
    bind:value={$searchQuery} 
    on:input={filterData} 
    placeholder="Search by name..." 
    class="w-80 p-2 border border-gray-300 rounded-md text-gray-700 shadow-sm focus:ring-2 focus:ring-lime-500" 
  />
  
    <!-- Gender Filter -->
    <select bind:value={selectedGender} on:change={filterData}
      class="p-2 w-22 border border-gray-300 rounded-md text-gray-700 shadow-sm focus:ring-2 focus:ring-lime-500">
      {#each genders as gender}
        <option value={gender}>{gender}</option>
      {/each}
    </select>
    <!-- Add button -->
    <button on:click={() => addModal = true} class="ml-[61vh] bg-orange-500 text-white px-5 py-2 rounded-lg hover:bg-orange-400 shadow ">+ Add Quiz</button>
    <!-- Date Filter -->
    <!-- <input type="date" bind:value={date} on:change={filterData}
      class="p-2 border border-gray-300 rounded-md text-gray-700 shadow-sm focus:ring-2 focus:ring-orange-500" /> -->
  </div>

  <!-- <div class="overflow-x-auto rounded-lg shadow-md"> -->
  <div class="max-h-[75vh] overflow-y-auto w-full">
    <table class="w-full  border-collapse bg-white rounded-lg text-sm">
      <thead class="sticky top-0 z-10">
        <tr class="bg-lime-500 text-white">
          {#each tableHeaders as header}
            <th class="p-2 cursor-pointer hover:bg-lime-400 transition" on:click={() => sortBy(header.key)}>
              {header.label}
              <span class="ml-1 text-xs">{($sortKey === header.key && $sortOrder === "asc") ? "▲" : "▼"}</span>
            </th>
          {/each}
        </tr>
      </thead>
      <tbody class="divide-y flex-1 overflow-auto">
        {#each $attendees as person}
          <tr class="border border-black/10 border-x-0 bg-gray-50 hover:bg-orange-200 transition transform"  on:click={() => openModal(person)}>
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
                        
            <td class="p-2 text-center justify-center">{person.level}</td>
            <td class="p-2 text-center justify-center">{person.ribbon}</td>
            <td class="p-2 text-center justify-center">{person.collectedTrash}</td>
          </tr>
        {/each}
      </tbody>
    </table>
  </div>
</div>

{#if $showModal}
  <Modal selectedPerson={$selectedPerson} on:close={closeModal} />
{/if}



