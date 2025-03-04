<script>
  import { onMount } from "svelte";
  import { studentData, studentNames } from '$lib/store/store_students';
  import { Table, TableBody, TableBodyCell, TableBodyRow, TableHead, TableHeadCell, TableSearch, Button, ButtonGroup } from 'flowbite-svelte';
  import { Section } from 'flowbite-svelte-blocks';
  import { ChevronRightOutline, ChevronLeftOutline } from 'flowbite-svelte-icons';
  
  let searchTerm = '';
  let currentPosition = 0;
  const itemsPerPage = 10;

  onMount(async () => {
    fetch("http://localhost/shenieva-teacher/src/lib/api/fetch_students.php")
      .then(response => response.json())
      .then(data => {
        studentData.set(data);
      })
      .catch(error => {
        console.error("Error fetching student data:", error);
        studentData.set([]);
      });
  });

  $: filteredItems = $studentData.filter((item) => item.studentName.toLowerCase().includes(searchTerm.toLowerCase()));
  $: totalItems = filteredItems.length;
  $: currentPageItems = filteredItems.slice(currentPosition, currentPosition + itemsPerPage);
  
  const loadNextPage = () => {
    if (currentPosition + itemsPerPage < totalItems) {
      currentPosition += itemsPerPage;
    }
  };

  const loadPreviousPage = () => {
    if (currentPosition - itemsPerPage >= 0) {
      currentPosition -= itemsPerPage;
    }
  };
</script>

<div class="h-screen flex flex-col">
  <div class="text-gray-500 font-bold text-2xl pl-10 pb-2">
    <h1>Students Management</h1>
  </div>

  <Section name="advancedTable" classSection='bg-gray-50 dark:bg-gray-900 p-3 sm:p-5'>
    <TableSearch placeholder="Search" hoverable={true} bind:inputValue={searchTerm}>
      <TableHead class="bg-orange-500 text-white">
        <TableHeadCell scope="col">Name</TableHeadCell>
        <TableHeadCell scope="col">Level</TableHeadCell>
        <TableHeadCell scope="col">Ribbon</TableHeadCell>
        <TableHeadCell scope="col">Collected Trash</TableHeadCell>
      </TableHead>

      <TableBody class="divide-y flex-1 overflow-auto">
        {#each currentPageItems as item (item.idNo)}
          <TableBodyRow class="hover:bg-orange-100">
            <TableBodyCell>{item.studentName}</TableBodyCell>
            <TableBodyCell>{item.studentLevel}</TableBodyCell>
            <TableBodyCell>{item.studentRibbon}</TableBodyCell>
            <TableBodyCell>{item.studentColtrash}</TableBodyCell>
          </TableBodyRow>
        {/each}
      </TableBody>

      <div slot="footer" class="flex justify-between p-4" aria-label="Table navigation">
        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
          Showing {currentPosition + 1}-{Math.min(currentPosition + itemsPerPage, totalItems)} of {totalItems}
        </span>
        <ButtonGroup>
          <Button on:click={loadPreviousPage} disabled={currentPosition === 0}>
            <ChevronLeftOutline size='xs' class='m-1.5'/>
          </Button>
          <Button on:click={loadNextPage} disabled={currentPosition + itemsPerPage >= totalItems}>
            <ChevronRightOutline size='xs' class='m-1.5'/>
          </Button>
        </ButtonGroup>
      </div>
    </TableSearch>
  </Section>
</div>