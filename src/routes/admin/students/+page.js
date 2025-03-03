/** @type {import('./$types').PageLoad} */
export async function load({ fetch }) {
    try {
        const res = await fetch('http://localhost/shenieva-teacher/api/fetch_students.php');

        if (!res.ok) {
            throw new Error(`Failed to fetch: ${res.status} ${res.statusText}`);
        }

        const students = await res.json();
        return { students };
    } catch (error) {
        console.error("Error fetching students:", error);
        return { students: [] };
    }
}
