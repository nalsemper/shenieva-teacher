<!-- src/routes/admin/quiz2.svelte -->
<script>
  import { onMount } from "svelte";
  import { writable, get } from "svelte/store";
  import QuizModal from "../modals/quizModal.svelte";
  import SuccessModal from "../modals/successModal.svelte";

  let selectedGender = "All";
  let date = new Date().toISOString().split("T")[0];
  let attendees = writable([]);
  let sortKey = writable("name");
  let sortOrder = writable("asc");
  let showModal = writable(false);
  let showSuccessModal = writable(false);
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

  const quiz2Questions = [
    {
      text: "What is the largest planet in our solar system?",
      correctAnswer: "Jupiter",
    },
    {
      text: "Which element has the atomic number 1?",
      correctAnswer: "Hydrogen",
    },
    {
      text: "What year did World War II end?",
      correctAnswer: "1945",
    },
  ];

  const data = [
    {
      name: "Alice Gou",
      gender: "Female",
      datetime: "2025-03-03 10:15",
      score: 90,
      attempts: 3,
      status: "Reviewed",
      questions: [
        { ...quiz2Questions[0], chosenAnswer: "Jupiter" },
        { ...quiz2Questions[1], chosenAnswer: "Hydrogen" },
        { ...quiz2Questions[2], chosenAnswer: "1945" },
      ],
    },
    {
      name: "Bob Ackerman",
      gender: "Male",
      datetime: "2025-03-03 11:00",
      score: 65,
      attempts: 2,
      status: "Pending for Review",
      questions: [
        { ...quiz2Questions[0], chosenAnswer: "Earth" },
        { ...quiz2Questions[1], chosenAnswer: "Hydrogen" },
        { ...quiz2Questions[2], chosenAnswer: "1939" },
      ],
    },
    {
      name: "Charlie Johnson",
      gender: "Male",
      datetime: "2025-03-03 12:30",
      score: 75,
      attempts: 1,
      status: "Reviewed",
      questions: [
        { ...quiz2Questions[0], chosenAnswer: "Jupiter" },
        { ...quiz2Questions[1], chosenAnswer: "Oxygen" },
        { ...quiz2Questions[2], chosenAnswer: "1945" },
      ],
    },
  ];

  function filterData() {
    attendees.set(
      data.filter((person) => {
        let personDate = person.datetime.split(" ")[0];
        return selectedGender === "All" || person.gender === selectedGender;
      })
    );
  }

  function sortBy(key) {
    sortKey.set(key);
    sortOrder.update((o) => (o === "asc" ? "desc" : "asc"));
    attendees.update((items) => {
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

  function openModal(person) {
    console.log("Opening modal for:", person);
    selectedPerson.set(person);
    showModal.set(true);
  }

  function closeModal() {
    console.log("Closing modal");
    showModal.set(false);
    selectedPerson.set(null);
  }

  function markAsReviewed() {
    console.log("Marking as reviewed:", $selectedPerson.name);
    $selectedPerson.status = "Reviewed";
    showSuccessModal.set(true);
  }

  function closeSuccessModal() {
    console.log("Closing success modal");
    showSuccessModal.set(false);
    closeModal();
  }

  onMount(() => {
    filterData();
  });
</script>

<div class="p-10 mb-14 mt-5 max-w-6xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden">
  <div class="flex items-center gap-4 mb-6 px-4">
    <select
      bind:value={selectedGender}
      on:change={filterData}
      class="p-2 w-24 border border-gray-300 rounded-lg text-gray-700 shadow-sm focus:ring-2 focus:ring-lime-500 bg-lime-200 cursor-pointer"
    >
      {#each genders as gender}
        <option value={gender}>{gender}</option>
      {/each}
    </select>
  </div>

  <div class="w-full mx-4 mb-4 rounded-3xl overflow-hidden shadow-lg bg-white">
    <table class="w-full text-sm">
      <thead class="sticky top-0 z-10 bg-lime-500 text-white">
        <tr>
          {#each tableHeaders as header, index}
            <th
              class="p-4 cursor-pointer hover:bg-lime-600 transition-all duration-200
                {index === 0 ? 'rounded-tl-3xl' : ''} 
                {index === tableHeaders.length - 1 ? 'rounded-tr-3xl' : ''}"
              on:click={() => sortBy(header.key)}
            >
              <span class="flex items-center justify-center gap-1">
                {header.label}
                <span class="text-xs"
                  >{$sortKey === header.key && $sortOrder === "asc"
                    ? "▲"
                    : "▼"}</span
                >
              </span>
            </th>
          {/each}
        </tr>
      </thead>
      <tbody>
        {#each $attendees as person, rowIndex}
          <tr
            class="hover:bg-orange-50 transition-all duration-200 cursor-pointer"
            on:click={() => openModal(person)}
          >
            <td
              class="p-4 font-medium 
                {rowIndex === $attendees.length - 1 ? 'rounded-bl-3xl' : ''}"
            >
              {person.name}
            </td>
            <td class="p-4 flex items-center justify-center">
              {#if person.gender === "Male"}
                <img
                  src="/src/assets/icons/male-sign.svg"
                  alt="Male Icon"
                  class="w-5 h-5"
                />
              {/if}
              {#if person.gender === "Female"}
                <img
                  src="/src/assets/icons/female-sign.svg"
                  alt="Female Icon"
                  class="w-5 h-5"
                />
              {/if}
            </td>
            <td class="p-4 text-center">{person.datetime}</td>
            <td class="p-4 text-center">
              <span
                class="inline-block px-3 py-1 rounded-full text-sm font-medium
                {person.score >= 80
                  ? 'bg-green-100 text-green-800'
                  : person.score >= 60
                    ? 'bg-orange-100 text-orange-800'
                    : 'bg-red-100 text-red-800'}"
              >
                {person.score} points
              </span>
            </td>
            <td class="p-4 text-center">{person.attempts}</td>
            <td
              class="p-4 text-center 
                {rowIndex === $attendees.length - 1 ? 'rounded-br-3xl' : ''}"
            >
              <span
                class="inline-block px-3 py-1 rounded-full text-sm font-medium
                {person.status === 'Reviewed'
                  ? 'bg-blue-100 text-blue-800'
                  : 'bg-orange-100 text-orange-800'}"
              >
                {person.status === "Reviewed" ? "Reviewed" : "Pending"}
              </span>
            </td>
          </tr>
        {/each}
      </tbody>
    </table>
  </div>
</div>

<QuizModal
  person={$selectedPerson}
  {showModal}
  {closeModal}
  {markAsReviewed}
/>

<SuccessModal
  showSuccessModal={$showSuccessModal}
  {closeSuccessModal}
  personName={$selectedPerson?.name || ""}
/>

<style>
  table {
    border-spacing: 0;
    width: 100%;
  }

  thead {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  tbody tr {
    background-color: #fafafa;
    transition: all 0.2s ease-in-out;
  }

  tbody tr:hover {
    background-color: #fef3e8;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  }

  button:hover {
    cursor: pointer;
  }

  th:hover {
    cursor: pointer;
  }

  tr:hover {
    cursor: pointer;
  }

  select:hover {
    cursor: pointer;
  }
</style>