// src/routes/login/+page.server.ts
import { fail, type RequestEvent } from '@sveltejs/kit';
import mysql from 'mysql2/promise';

const dbConfig = {
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'shenieva_db'
};

interface Teacher extends mysql.RowDataPacket {
  pk_teacherID: number;
  idNo: string;
  teacherName: string;
  teacherEmail: string;
  teacherPass: string;
  teacherGender: 'Male' | 'Female' | 'Other';
}

export const actions = {
  default: async ({ request, cookies }: RequestEvent) => {
    let formDataEntries;
    try {
      console.log('Processing login request...');
      const formData = await request.formData();
      formDataEntries = Object.fromEntries(formData);
      const userType = formData.get('userType')?.toString();
      const idNo = formData.get('idNo')?.toString();
      const password = formData.get('password')?.toString();

      console.log('Form data received:', { userType, idNo, password });

      if (!userType || !idNo || !password) {
        return fail(400, { error: 'Missing required fields' });
      }

      if (userType === 'teacher') {
        let connection;
        try {
          console.log('Attempting database connection...');
          connection = await mysql.createConnection(dbConfig);
          await connection.connect();
          console.log('Database connected successfully');

          const [rows] = await connection.execute<Teacher[]>(
            'SELECT * FROM teacher_table WHERE idNo = ?',
            [idNo]
          );
          console.log('Query executed, rows:', rows);

          if (!Array.isArray(rows) || rows.length === 0) {
            return fail(401, { error: 'Invalid teacher ID or password' });
          }

          const teacher = rows[0];
          console.log('Teacher found:', {
            idNo: teacher.idNo,
            pass: teacher.teacherPass
          });

          if (password !== teacher.teacherPass) {
            return fail(401, { error: 'Invalid teacher ID or password' });
          }

          cookies.set('teacherId', teacher.pk_teacherID.toString(), {
            path: '/',
            httpOnly: true,
            secure: process.env.NODE_ENV === 'production',
            maxAge: 60 * 60 * 24 // 1 day
          });

          return {
            success: true,
            userType: 'teacher',
            teacherId: teacher.pk_teacherID,
            teacherName: teacher.teacherName
          };
        } catch (dbError) {
          console.error('Database error:', dbError);
          throw dbError;
        } finally {
          if (connection) {
            await connection.end().catch(err => console.error('Error closing connection:', err));
          }
        }
      }

      return fail(400, { error: 'Invalid user type' });
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Unknown error occurred';
      console.error('Detailed login error:', { message: errorMessage, formData: formDataEntries });
      return fail(500, { error: 'Internal server error', details: errorMessage });
    }
  }
};