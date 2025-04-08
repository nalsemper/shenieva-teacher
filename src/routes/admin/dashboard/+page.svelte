<script>
  import { Card, Button, Modal, Chart } from "flowbite-svelte";
  import {
    ExclamationCircleOutline,
    UsersGroupSolid,
    NewspaperSolid,
    BookOpenOutline,
    AwardOutline,
  } from "flowbite-svelte-icons";
  import { goto } from "$app/navigation";
  import SettingsModal from "../modals/settings.svelte";
  import modalBg from "/src/assets/icons/modal-bg.jpg";

  let showMenu = false;
  let showLogoutModal = false;
  let showSettingsModal = false;

  let studentCount = 256;
  let quizzesTaken = 128;

  // Quiz data for Quiz 1, Quiz 2, Quiz 3
  let quizData = [
    { name: "Quiz 1", scores: [85, 90, 75, 80, 95] }, // Scores for Alice, Bob, Charlie, David, Eve
    { name: "Quiz 2", scores: [88, 85, 70, 82, 90] },
    { name: "Quiz 3", scores: [80, 92, 78, 85, 93] },
  ];
  let studentNames = ["Alice", "Bob", "Charlie", "David", "Eve"];

  // Male/Female data for pie chart
  let genderData = {
    series: [150, 106], // Males, Females (summing to studentCount)
    labels: ["Male", "Female"],
  };

  $: options = {
    series: quizData.map(quiz => ({
      name: quiz.name,
      data: quiz.scores,
      color: quiz.name === "Quiz 1" ? "#4CAF50" : quiz.name === "Quiz 2" ? "#2196F3" : "#FF9800",
    })),
    chart: {
      type: "bar",
      height: 230, // Back to fixed height
      toolbar: { show: false },
    },
    plotOptions: {
      bar: {
        horizontal: true,
        columnWidth: "60%",
        borderRadius: 6,
      },
    },
    dataLabels: { enabled: false },
    xaxis: {
      categories: [...studentNames],
      labels: {
        style: {
          fontFamily: "Inter, sans-serif",
          fontSize: "12px",
          cssClass: "text-xs font-normal text-gray-600",
        },
      },
    },
    yaxis: {
      labels: {
        style: {
          fontFamily: "Inter, sans-serif",
          fontSize: "12px",
          cssClass: "text-xs font-normal text-gray-600",
        },
      },
    },
    grid: {
      strokeDashArray: 4,
      padding: { left: 10, right: 10, top: -10 },
    },
    legend: {
      position: "top",
      horizontalAlign: "left",
      fontFamily: "Inter, sans-serif",
      fontSize: "12px",
    },
  };

  let attendanceOptions = {
    chart: {
      height: 200,
      maxWidth: "100%",
      type: "area",
      fontFamily: "Inter, sans-serif",
      dropShadow: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    tooltip: {
      enabled: true,
      x: {
        show: false,
      },
    },
    fill: {
      type: "gradient",
      gradient: {
        opacityFrom: 0.55,
        opacityTo: 0,
        shade: "#FF5733",
        gradientToColors: ["#FF5733"],
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      width: 6,
      curve: "smooth",
    },
    grid: {
      show: true,
      strokeDashArray: 4,
      padding: {
        left: 10,
        right: 10,
        top: 0,
      },
    },
    series: [
      {
        name: "Attendance",
        data: [42, 48, 46, 50, 47, 53, 49],
        color: "#FF5733",
      },
    ],
    xaxis: {
      categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
      labels: {
        show: true,
        rotate: -45,
        trim: true,
        style: {
          fontFamily: "Inter, sans-serif",
          fontSize: "12px",
          cssClass: "text-xs font-normal text-gray-600",
        },
      },
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
    },
    yaxis: {
      show: true,
      labels: {
        style: {
          fontFamily: "Inter, sans-serif",
          fontSize: "12px",
          cssClass: "text-xs font-normal text-gray-600",
        },
      },
    },
  };

  // Pie chart options for Male/Female count
  let genderOptions = {
    series: genderData.series,
    colors: ["#2196F3", "#FF4081"], // Blue for Male, Pink for Female
    chart: {
      height: 200,
      width: "100%",
      type: "pie",
      toolbar: { show: false },
    },
    stroke: {
      colors: ["white"],
      lineCap: "",
    },
    plotOptions: {
      pie: {
        labels: {
          show: true,
        },
        size: "100%",
        dataLabels: {
          offset: -25,
        },
      },
    },
    labels: genderData.labels,
    dataLabels: {
      enabled: true,
      style: {
        fontFamily: "Inter, sans-serif",
      },
      formatter: function (val, opts) {
        return opts.w.config.series[opts.seriesIndex]; // Show raw numbers
      },
    },
    legend: {
      position: "bottom",
      fontFamily: "Inter, sans-serif",
    },
    yaxis: {
      labels: {
        formatter: function (value) {
          return value; // Raw numbers
        },
      },
    },
    xaxis: {
      labels: {
        formatter: function (value) {
          return value; // Raw numbers
        },
      },
      axisTicks: {
        show: false,
      },
      axisBorder: {
        show: false,
      },
    },
    tooltip: {
      y: {
        formatter: function (value) {
          return value + " students";
        },
      },
    },
  };

  // Podium data for top 3 students
  let topStudents = [
    { name: "Eve", totalScore: 278, place: 1 },
    { name: "Bob", totalScore: 267, place: 2 },
    { name: "Alice", totalScore: 253, place: 3 },
  ];

  function toggleMenu() {
    showMenu = !showMenu;
  }

  function navigateToProfile() {
    goto("/admin/profile");
    showMenu = false;
  }

  function logout() {
    console.log("User logged out");
    goto("../");
  }
</script>

<div class="h-screen flex flex-col">
  <!-- Main Content -->
  <div class="flex-1 flex flex-col">
    <!-- Fixed Top Bar -->
    <div
      class="fixed top-0 left-64 w-[calc(100%-16rem)] bg-orange-500 text-white px-6 flex justify-between items-center shadow-md z-50 h-16"
    >
      <h1 class="text-xl font-semibold">Dashboard</h1>

      <!-- Avatar & Dropdown -->
      <div class="relative p-2">
        <button
          class="w-10 h-10 rounded-full overflow-hidden border-2 border-white"
          on:click={toggleMenu}
        >
          <img
            src="/avatar.jpg"
            alt="User Avatar"
            class="w-full h-full rounded-full border-4 border-gray-300 object-cover"
          />
        </button>

        {#if showMenu}
          <div
            class="absolute right-0 mt-2 w-48 bg-white text-black shadow-lg rounded-md py-2"
          >
            <button
              on:click={navigateToProfile}
              class="block w-full text-left px-4 py-2 hover:bg-gray-200"
              >Profile</button
            >
            <button
              on:click={() => (showSettingsModal = true)}
              class="block w-full text-left px-4 py-2 hover:bg-gray-200"
              >Settings</button
            >
            <button
              class="block w-full text-left px-4 py-2 hover:bg-gray-200"
              on:click={() => (showLogoutModal = true)}>Logout</button
            >
          </div>
        {/if}
      </div>
    </div>

    <!-- Grid Container - Reduced Size -->
    <div
      class="grid grid-cols-2 gap-6 max-h-[100vh] mt-20 px-4 w-[calc(100%-1rem)]"
    >
      <!-- Left Column (3 Rows) -->
      <div
        class="grid gap-4"
        style="display: grid; grid-template-rows: 1fr 2fr 2fr;"
      >
        <!-- Row 1: Two Divs Side by Side -->
        <div class="grid grid-cols-2 gap-4">
          <!-- Student Count Card -->
          <div
            class="bg-blue-500 p-4 rounded-xl shadow-md flex items-center justify-between text-white"
          >
            <UsersGroupSolid class="w-12 h-12 opacity-80" />
            <div class="text-right">
              <h2 class="text-3xl font-bold">{studentCount}</h2>
              <p class="text-base font-medium">Total Students</p>
            </div>
          </div>

          <!-- Another card (Row 1 - Right) -->
          <div
            class="bg-green-500 p-4 rounded-xl shadow-md flex items-center justify-between text-white"
          >
            <NewspaperSolid class="w-12 h-12 opacity-80" />
            <div class="text-right">
              <h2 class="text-3xl font-bold">{quizzesTaken}</h2>
              <p class="text-base font-medium">Quizzes Taken</p>
            </div>
          </div>
        </div>

        <!-- Row 2: Bar Chart for Quiz Points -->
        <div class="bg-blue-100 p-3 rounded-xl shadow-md">
          <div class="flex justify-between items-center border-b pb-2">
            <h3 class="text-base font-semibold text-gray-800">
              Quiz Performance
            </h3>
            <AwardOutline class="w-5 h-5 text-gray-500" />
          </div>
          <div class="py-2">
            <Chart {options} />
          </div>
        </div>

        <!-- Row 3: Attendance Line Graph (Restored) -->
        <div class="bg-yellow-100 p-3 rounded-xl shadow-md">
          <div class="flex justify-between items-center border-b pb-2">
            <h3 class="text-base font-semibold text-gray-800">
              Attendance Trend
            </h3>
            <BookOpenOutline class="w-5 h-5 text-gray-500" />
          </div>
          <div class="py-2">
            <Chart options={attendanceOptions} />
          </div>
        </div>
      </div>

      <!-- Right Column (3 Rows) -->
      <div
        class="grid gap-4"
        style="display: grid; grid-template-rows: 2fr 2fr 2fr;"
      >
        <div
          class="p-3 rounded-xl shadow-md bg-black bg-opacity-50"
          style="background-image: url('/src/assets/shenievia.png'); background-size: cover; background-position: center;"
        ></div>
        <!-- Row 2: Gender Pie Chart (Full Width) -->
        <div class="bg-green-100 p-3 rounded-xl shadow-md">
          <div class="flex justify-between items-center border-b pb-2">
            <h3 class="text-base font-semibold text-gray-800">
              Gender Distribution
            </h3>
            <UsersGroupSolid class="w-5 h-5 text-gray-500" />
          </div>
          <div class="py-2">
            <Chart options={genderOptions} />
          </div>
        </div>
        <!-- Row 3: Podium for Top 3 Students -->
        <div class="bg-slate-100 p-3 rounded-xl shadow-md">
          <div class="flex justify-between items-center border-b pb-2">
            <h3 class="text-base font-semibold text-gray-800">
              Top Quiz Completers
            </h3>
            <AwardOutline class="w-5 h-5 text-gray-500" />
          </div>
          <div class="flex justify-around items-end h-[calc(100%-theme(spacing.12))] py-2">
            <!-- 2nd Place -->
            <div class="text-center w-1/3">
              <div class="bg-silver-300 h-20 rounded-t-md flex items-center justify-center text-white font-bold">
                {topStudents[1].name}
              </div>
              <div class="bg-silver-500 h-10 text-white flex items-center justify-center">
                2nd
              </div>
              <div class="text-gray-800 text-sm">{topStudents[1].totalScore} pts</div>
            </div>
            <!-- 1st Place -->
            <div class="text-center w-1/3">
              <div class="bg-gold-300 h-28 rounded-t-md flex items-center justify-center text-white font-bold">
                {topStudents[0].name}
              </div>
              <div class="bg-gold-500 h-10 text-white flex items-center justify-center">
                1st
              </div>
              <div class="text-gray-800 text-sm">{topStudents[0].totalScore} pts</div>
            </div>
            <!-- 3rd Place -->
            <div class="text-center w-1/3">
              <div class="bg-bronze-300 h-16 rounded-t-md flex items-center justify-center text-white font-bold">
                {topStudents[2].name}
              </div>
              <div class="bg-bronze-500 h-10 text-white flex items-center justify-center">
                3rd
              </div>
              <div class="text-gray-800 text-sm">{topStudents[2].totalScore} pts</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Logout Confirmation Modal -->
<Modal bind:open={showLogoutModal} size="xs" autoclose>
  <div class="text-center">
    <ExclamationCircleOutline
      class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200"
    />
    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
      Are you sure you want to logout?
    </h3>
    <Button on:click={logout} color="red" class="me-2">Yes, I'm sure</Button>
    <Button on:click={() => (showLogoutModal = false)} color="alternative"
      >No, cancel</Button
    >
  </div>
</Modal>

<!-- Settings Modal -->
<SettingsModal bind:open={showSettingsModal} />

<style>
  .bg-silver-300 {
    background-color: #d3d3d3; /* Light silver */
  }
  .bg-silver-500 {
    background-color: #a9a9a9; /* Darker silver */
  }
  .bg-gold-300 {
    background-color: #ffd700; /* Light gold */
  }
  .bg-gold-500 {
    background-color: #daa520; /* Darker gold */
  }
  .bg-bronze-300 {
    background-color: #cd7f32; /* Light bronze */
  }
  .bg-bronze-500 {
    background-color: #8c5523; /* Darker bronze */
  }
</style>