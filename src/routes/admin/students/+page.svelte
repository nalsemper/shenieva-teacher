<script>
  import { onMount } from 'svelte';
  import { writable, get } from "svelte/store";
  import { Table, TableBody, TableBodyCell, TableBodyRow, TableHead, TableHeadCell, TableSearch, Button, Dropdown, DropdownItem, Checkbox, ButtonGroup } from 'flowbite-svelte';
  import { Section } from 'flowbite-svelte-blocks';
  import { PlusOutline, ChevronDownOutline, FilterSolid, ChevronRightOutline, ChevronLeftOutline } from 'flowbite-svelte-icons';

  let paginationData = [
    { id: 1, name: "John Doe", level: "Grade 10", ribbon: "Gold", collected_trash: "12kg" },
    { id: 2, name: "Jane Smith", level: "Grade 11", ribbon: "Silver", collected_trash: "8kg" },
    { id: 3, name: "Michael Brown", level: "Grade 12", ribbon: "Bronze", collected_trash: "5kg" },
    { id: 4, name: "Emily Johnson", level: "Grade 10", ribbon: "Gold", collected_trash: "14kg" },
    { id: 5, name: "David Wilson", level: "Grade 11", ribbon: "Silver", collected_trash: "10kg" },
    { id: 6, name: "Sophia Martinez", level: "Grade 12", ribbon: "Bronze", collected_trash: "6kg" },
    { id: 7, name: "James Anderson", level: "Grade 10", ribbon: "Gold", collected_trash: "13kg" },
    { id: 8, name: "Olivia Thomas", level: "Grade 11", ribbon: "Silver", collected_trash: "9kg" },
    { id: 9, name: "William Hernandez", level: "Grade 12", ribbon: "Bronze", collected_trash: "7kg" },
    { id: 10, name: "Ava Miller", level: "Grade 10", ribbon: "Gold", collected_trash: "15kg" }
  ];

  let divClass = 'bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden';
  let innerDivClass = 'flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4';
  let searchClass = 'w-full md:w-1/2 relative';
  let classInput = "text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 pl-10";

  let searchTerm = '';
  let currentPosition = 0;
  const itemsPerPage = 10;
  const showPage = 5;
  let totalItems = paginationData.length;
  let totalPages = Math.ceil(totalItems / itemsPerPage);
  let startPage = 1;
  let endPage = Math.min(showPage, totalPages);
  let pagesToShow = [];

  const updatePagination = () => {
    totalPages = Math.ceil(totalItems / itemsPerPage);
    const currentPage = Math.ceil((currentPosition + 1) / itemsPerPage);

    startPage = Math.max(1, currentPage - Math.floor(showPage / 2));
    endPage = Math.min(startPage + showPage - 1, totalPages);

    if (endPage > totalPages) {
      endPage = totalPages;
    }

    pagesToShow = Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
  };

  const loadNextPage = () => {
    if (currentPosition + itemsPerPage < totalItems) {
      currentPosition += itemsPerPage;
      updatePagination();
    }
  };

  const loadPreviousPage = () => {
    if (currentPosition - itemsPerPage >= 0) {
      currentPosition -= itemsPerPage;
      updatePagination();
    }
  };

  const goToPage = (pageNumber) => {
    currentPosition = (pageNumber - 1) * itemsPerPage;
    updatePagination();
  };

  $: filteredItems = paginationData.filter((item) => item.name.toLowerCase().includes(searchTerm.toLowerCase()));
  $: totalItems = filteredItems.length;
  $: updatePagination();
  $: currentPageItems = filteredItems.slice(currentPosition, currentPosition + itemsPerPage);
  $: startRange = currentPosition + 1;
  $: endRange = Math.min(currentPosition + itemsPerPage, totalItems);

  onMount(() => {
    updatePagination();
  });


</script>


<div class="h-screen flex flex-col">
  <div class="text-gray-500 font-bold text-2xl pl-10 pb-2">
    <h1>Students Management</h1>
  </div>

  <Section name="advancedTable" classSection='bg-gray-50 dark:bg-gray-900 p-3 sm:p-5 '>
    <TableSearch placeholder="Search" hoverable={true} bind:inputValue={searchTerm} {divClass} {innerDivClass} {searchClass} {classInput} >
      <div slot="header" class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 ">
        <button on:click={() => showModal = true} class="bg-lime-500 text-white px-5 py-2 rounded-lg hover:bg-lime-600 shadow ">+ Add Student</button>
      </div>

      <TableHead class="bg-orange-500 text-white">
        <TableHeadCell padding="px-4 py-3" scope="col">Name</TableHeadCell>
        <TableHeadCell padding="px-4 py-3" scope="col">Level</TableHeadCell>
        <TableHeadCell padding="px-4 py-3" scope="col">Ribbon</TableHeadCell>
        <TableHeadCell padding="px-4 py-3" scope="col">Collected Trash</TableHeadCell>
      </TableHead>
      
      <TableBody class="divide-y flex-1 overflow-auto ">
        {#each currentPageItems as item (item.id)}
          <TableBodyRow class="hover:bg-orange-100">
            <TableBodyCell tdClass="px-4 py-3">{item.name}</TableBodyCell>
            <TableBodyCell tdClass="px-4 py-3">{item.level}</TableBodyCell>
            <TableBodyCell tdClass="px-4 py-3">{item.ribbon}</TableBodyCell>
            <TableBodyCell tdClass="px-4 py-3">{item.collected_trash}</TableBodyCell>
          </TableBodyRow>
        {/each}
      </TableBody>

      <div slot="footer" class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4" aria-label="Table navigation">
        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
          Showing <span class="font-semibold text-gray-900 dark:text-white">{startRange}-{endRange}</span> of <span class="font-semibold text-gray-900 dark:text-white">{totalItems}</span>
        </span>
        <ButtonGroup>
          <Button on:click={loadPreviousPage} disabled={currentPosition === 0}>
            <ChevronLeftOutline size='xs' class='m-1.5'/>
          </Button>
          {#each pagesToShow as pageNumber}
          <Button on:click={() => goToPage(pageNumber)} class={currentPosition === (pageNumber - 1) * itemsPerPage ? 'active' : ''}>
              {pageNumber}
            </Button>
          {/each}
          <Button on:click={loadNextPage} disabled={currentPosition + itemsPerPage >= totalItems}>
            <ChevronRightOutline size='xs' class='m-1.5'/>
          </Button>
        </ButtonGroup>
      </div>
    </TableSearch>
  </Section>
</div>
