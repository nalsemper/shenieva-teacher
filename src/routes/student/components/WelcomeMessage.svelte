<!-- src/routes/student/components/WelcomeMessage.svelte -->
<script lang="ts">
  import { name } from '$lib/store/nameStore';
  import { studentData } from '$lib/store/student_data';
  import { goto } from '$app/navigation';
  import { onMount } from 'svelte';

  export let gender: 'boy' | 'girl';
  const dbGender: 'Male' | 'Female' = gender === 'boy' ? 'Male' : 'Female';

  async function updateStudentGender() {
    if (!$studentData) {
      console.error('No student data available');
      return;
    }

    try {
      const response = await fetch('http://localhost/shenieva-teacher/src/lib/api/update_studentData.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          pk_studentID: $studentData.pk_studentID,
          studentGender: dbGender,
        }),
      });

      if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

      const result = await response.json();
      if (result.success) {
        studentData.update((data) => ({
          ...data!,
          studentGender: dbGender,
        }));
        await recordAttendance();
      } else {
        console.error('Gender update failed:', result.message);
      }
    } catch (error) {
      console.error('Error updating gender:', error);
    }
  }

  async function recordAttendance() {
    if (!$studentData) return;

    try {
      const response = await fetch('http://localhost/shenieva-teacher/src/lib/api/record_attendance.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          fk_studentID: $studentData.pk_studentID,
          studentName: $name, // Add the student's name from the store
          attendanceDateTime: new Date().toISOString(),
        }),
      });

      if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

      const result = await response.json();
      if (!result.success) {
        console.error('Attendance recording failed:', result.message);
      }
    } catch (error) {
      console.error('Error recording attendance:', error);
    }
  }

  onMount(() => {
    updateStudentGender();
    setTimeout(() => {
      goto('/student/dashboard');
    }, 10000);
  });
</script>

<div class="bg-gray-100 p-8 rounded-3xl shadow-lg w-full max-w-md border-4 border-lime-400 text-center">
  <h1 class="text-3xl font-bold text-lime-600 mb-6" style="font-family: 'Comic Sans MS', 'Chalkboard', cursive;">
    Welcome, {$name}! 🎉
  </h1>
  <p class="text-xl text-gray-700 mb-4" style="font-family: 'Comic Sans MS', 'Chalkboard', cursive;">
    You’re a super {gender}! Getting ready for fun...
  </p>
  <div class="text-5xl animate-bounce">✨</div>
</div>

<style>
  @keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
  }
  .animate-bounce {
    animation: bounce 1s infinite;
  }
</style>