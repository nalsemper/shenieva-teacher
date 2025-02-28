<script>
    import { onMount } from "svelte";
    import { writable, get } from "svelte/store";
  
    let selectedGender = "All";
    let date = new Date().toISOString().split("T")[0]; // Default to today's date
    let attendees = writable([]);
    let sortKey = writable("name");
    let sortOrder = writable("asc");
  
    const genders = ["All", "Male", "Female"];
  
    const tableHeaders = [
      { key: "name", label: "Name" },
      { key: "gender", label: "Gender" },
      { key: "datetime", label: "Date & Time" },
      { key: "quiz1", label: "Quiz 1" },
      { key: "quiz2", label: "Quiz 2" },
      { key: "quiz3", label: "Quiz 3" },
      { key: "progress", label: "Progress" },
    ];
  
    const data = [
  { name: "Alice Gou", gender: "Female", progress: 60, quiz1: 85, quiz2: 90, quiz3: 88, datetime: "2025-02-27 14:30" },
  { name: "Bob Ackerman", gender: "Male", progress: 20, quiz1: 80, quiz2: 78, quiz3: 82, datetime: "2025-02-27 15:00" },
  { name: "Charlie Johnson", gender: "Male", progress: 40, quiz1: 60, quiz2: 70, quiz3: 15, datetime: "2025-02-27 15:45" },
  { name: "David Kelly", gender: "Male", progress: 80, quiz1: 61, quiz2: 30, quiz3: 65, datetime: "2025-02-27 16:15" },
  { name: "Eve Nighcast", gender: "Female", progress: 100, quiz1: 20, quiz2: 20, quiz3: 35, datetime: "2025-02-27 17:00" },
  { name: "Frank Mendez", gender: "Male", progress: 60, quiz1: 75, quiz2: 80, quiz3: 77, datetime: "2025-02-27 18:00" },
  { name: "Grace Hopper", gender: "Female", progress: 40, quiz1: 88, quiz2: 85, quiz3: 82, datetime: "2025-02-27 19:15" },
  { name: "Hannah Lee", gender: "Female", progress: 80, quiz1: 55, quiz2: 65, quiz3: 72, datetime: "2025-02-27 20:30" },
  { name: "Ian McArthur", gender: "Male", progress: 20, quiz1: 90, quiz2: 92, quiz3: 88, datetime: "2025-02-27 21:45" },
  { name: "Jackie Chan", gender: "Male", progress: 100, quiz1: 33, quiz2: 42, quiz3: 50, datetime: "2025-02-27 22:00" },
  { name: "Kylie Jenner", gender: "Female", progress: 60, quiz1: 77, quiz2: 80, quiz3: 85, datetime: "2025-02-27 23:15" },
  { name: "Leo Messi", gender: "Male", progress: 40, quiz1: 65, quiz2: 70, quiz3: 60, datetime: "2025-02-27 10:45" },
  { name: "Mona Lisa", gender: "Female", progress: 80, quiz1: 82, quiz2: 79, quiz3: 85, datetime: "2025-02-27 11:30" },
  { name: "Nathan Drake", gender: "Male", progress: 100, quiz1: 50, quiz2: 60, quiz3: 55, datetime: "2025-02-27 12:15" },
  { name: "Olivia Wilde", gender: "Female", progress: 20, quiz1: 95, quiz2: 89, quiz3: 92, datetime: "2025-02-27 13:30" },
  { name: "Paul Newman", gender: "Male", progress: 60, quiz1: 79, quiz2: 85, quiz3: 83, datetime: "2025-02-27 14:45" },
  { name: "Quincy Adams", gender: "Male", progress: 40, quiz1: 62, quiz2: 58, quiz3: 55, datetime: "2025-02-27 16:00" },
  { name: "Rachel Green", gender: "Female", progress: 80, quiz1: 72, quiz2: 75, quiz3: 80, datetime: "2025-02-27 17:15" },
  { name: "Samuel Jackson", gender: "Male", progress: 100, quiz1: 45, quiz2: 50, quiz3: 48, datetime: "2025-02-27 18:30" },
  { name: "Tina Fey", gender: "Female", progress: 20, quiz1: 89, quiz2: 91, quiz3: 88, datetime: "2025-02-27 19:45" },
  { name: "Ursula K. Le Guin", gender: "Female", progress: 40, quiz1: 58, quiz2: 60, quiz3: 65, datetime: "2025-02-27 21:00" },
  { name: "Victor Hugo", gender: "Male", progress: 60, quiz1: 74, quiz2: 78, quiz3: 80, datetime: "2025-02-27 22:15" },
  { name: "Wanda Maximoff", gender: "Female", progress: 80, quiz1: 85, quiz2: 88, quiz3: 90, datetime: "2025-02-27 23:30" },
  { name: "Xander Cage", gender: "Male", progress: 100, quiz1: 55, quiz2: 60, quiz3: 65, datetime: "2025-02-27 09:00" }
];

  
    function filterData() {
      attendees.set(
        data.filter(person => {
          let personDate = person.datetime.split(" ")[0]; // Extract only the date part
          return (selectedGender === "All" || person.gender === selectedGender) &&
                 (personDate === date); // Filter by selected date
        })
      );
    }
  
    function sortBy(key) {
      sortKey.set(key);
      sortOrder.update(o => (o === "asc" ? "desc" : "asc"));
  
      attendees.update(items => {
        return [...items].sort((a, b) => {
          let valA = a[key];
          let valB = b[key];
  
          if (typeof valA === "string") {
            return valA.localeCompare(valB) * (get(sortOrder) === "asc" ? 1 : -1);
          } else {
            return (valA - valB) * (get(sortOrder) === "asc" ? 1 : -1);
          }
        });
      });
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
      <input type="date" bind:value={date} on:change={filterData}
        class="p-2 border border-gray-300 rounded-md text-gray-700 shadow-sm focus:ring-2 focus:ring-orange-500" />
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
            <tr class="border-b last:border-none bg-gray-50 hover:bg-orange-100 transition">
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
              <td class="p-2 text-center justify-center">{person.quiz1}</td>
              <td class="p-2 text-center justify-center">{person.quiz2}</td>
              <td class="p-2 text-center justify-center">{person.quiz3}</td>
              <td class="p-2">
                <div class="w-full bg-gray-300 rounded-xl h-4 relative flex items-center text-xs text-gray-700 z-0">
                  <span class="absolute w-full text-center">
                    {#if person.progress == 20 } Intro {/if}
                    {#if person.progress == 40 } Story 1 {/if}
                    {#if person.progress == 60 } Story 2 {/if}
                    {#if person.progress == 80 } Story 3 {/if}
                    {#if person.progress == 100 } Finished {/if}
                  </span>
                  <div class="h-4 rounded-xl"
                    style="width: {person.progress}%; background: linear-gradient(to right, 
                      {person.progress == 20 ? 'orangered, orange' :
                       person.progress == 40 ? 'orange, yellow' :
                       person.progress == 60 ? 'yellow, lawngreen' :
                       person.progress == 80 ? 'lawngreen, lime' :
                       'lime, limegreen'})">
                  </div>
                </div>
              </td>
            </tr>
          {/each}
        </tbody>
      </table>
    </div>
  </div>
  