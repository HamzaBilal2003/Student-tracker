<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * OPTIONAL demo data seeder.
 *
 * Run with:  php artisan db:seed --class=DemoDataSeeder
 *
 * This is NOT required to boot the application. Run the default seeder
 * (php artisan db:seed) first to create the login account, then run this
 * one only if you want realistic content to click through in the dashboard.
 *
 * It is safe to run more than once: every record is matched on a natural
 * key and only created when it does not already exist.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Always make sure a login account exists first, so running this
        // seeder on its own (e.g. `migrate:fresh --seed --seeder=DemoDataSeeder`)
        // can never leave the app with no way to log in.
        $this->call(DatabaseSeeder::class);

        $categories = $this->seedCategories();
        $courses = $this->seedCourses($categories);
        $teachers = $this->seedTeachers($categories);
        $students = $this->seedStudents($courses, $teachers);

        $this->seedPayments($students);
        $this->seedExpenses();

        $this->command?->newLine();
        $this->command?->info('Demo data created: 4 categories, 8 courses, 6 teachers, '.count($students).' students, payments and monthly expenses.');
        $this->command?->comment('Login with test@example.com / password');
    }

    /**
     * categories are the top level grouping (e.g. Web Development).
     *
     * @return array<string, Category> keyed by slug-ish name
     */
    protected function seedCategories(): array
    {
        $definitions = [
            'web' => ['name' => 'Web Development', 'status' => 1],
            'graphic' => ['name' => 'Graphic Design', 'status' => 1],
            'office' => ['name' => 'Office & Computer', 'status' => 1],
            'marketing' => ['name' => 'Digital Marketing', 'status' => 1],
        ];

        $categories = [];

        foreach ($definitions as $key => $attributes) {
            $categories[$key] = Category::updateOrCreate(
                ['name' => $attributes['name']],
                ['status' => $attributes['status']]
            );
        }

        return $categories;
    }

    /**
     * @param  array<string, Category>  $categories
     * @return array<string, Course>
     */
    protected function seedCourses(array $categories): array
    {
        $definitions = [
            'laravel' => ['name' => 'Laravel Web Development', 'category' => 'web', 'price' => 45000, 'duration' => '6 Months', 'languages' => 'Urdu, English', 'description' => 'Full stack web development with PHP, Laravel, MySQL and REST APIs.'],
            'frontend' => ['name' => 'Front-End Development', 'category' => 'web', 'price' => 38000, 'duration' => '4 Months', 'languages' => 'Urdu, English', 'description' => 'HTML, CSS, JavaScript and Bootstrap with responsive layouts.'],
            'php' => ['name' => 'PHP & MySQL', 'category' => 'web', 'price' => 32000, 'duration' => '3 Months', 'languages' => 'Urdu', 'description' => 'Dynamic websites and database driven applications from scratch.'],
            'graphics' => ['name' => 'Graphic Design', 'category' => 'graphic', 'price' => 30000, 'duration' => '3 Months', 'languages' => 'Urdu, English', 'description' => 'Photoshop, Illustrator and CorelDRAW for print and digital artwork.'],
            'video' => ['name' => 'Video Editing', 'category' => 'graphic', 'price' => 28000, 'duration' => '2 Months', 'languages' => 'Urdu', 'description' => 'Premiere Pro and After Effects for reels and short films.'],
            'office' => ['name' => 'MS Office & Computer Course', 'category' => 'office', 'price' => 12000, 'duration' => '1 Month', 'languages' => 'Urdu', 'description' => 'Word, Excel, PowerPoint and basic typing practice.'],
            'basic' => ['name' => 'Basic Computer Course', 'category' => 'office', 'price' => 8000, 'duration' => '1 Month', 'languages' => 'Urdu', 'description' => 'Hardware basics, operating system and internet essentials.'],
            'marketing' => ['name' => 'Digital Marketing', 'category' => 'marketing', 'price' => 35000, 'duration' => '3 Months', 'languages' => 'Urdu, English', 'description' => 'SEO, social media advertising and Google Ads campaigns.'],
        ];

        $courses = [];

        foreach ($definitions as $key => $row) {
            $courses[$key] = Course::updateOrCreate(
                ['name' => $row['name']],
                [
                    'category_id' => $categories[$row['category']]->id,
                    'description' => $row['description'],
                    'price' => $row['price'],
                    'duration' => $row['duration'],
                    'languages' => $row['languages'],
                ]
            );
        }

        return $courses;
    }

    /**
     * @param  array<string, Category>  $categories
     * @return array<string, Teacher>
     */
    protected function seedTeachers(array $categories): array
    {
        $definitions = [
            'ahmed' => ['name' => 'Ahmed Raza', 'category' => 'web', 'exp' => '5 Years', 'phone' => '0300-1234501', 'email' => 'ahmed.raza@example.com', 'salary' => 60000],
            'bilal' => ['name' => 'Bilal Khan', 'category' => 'web', 'exp' => '3 Years', 'phone' => '0300-1234502', 'email' => 'bilal.khan@example.com', 'salary' => 45000],
            'sana' => ['name' => 'Sana Malik', 'category' => 'graphic', 'exp' => '4 Years', 'phone' => '0300-1234503', 'email' => 'sana.malik@example.com', 'salary' => 50000],
            'usman' => ['name' => 'Usman Tariq', 'category' => 'graphic', 'exp' => '2 Years', 'phone' => '0300-1234504', 'email' => 'usman.tariq@example.com', 'salary' => 40000],
            'hina' => ['name' => 'Hina Aslam', 'category' => 'office', 'exp' => '6 Years', 'phone' => '0300-1234505', 'email' => 'hina.aslam@example.com', 'salary' => 55000],
            'kashif' => ['name' => 'Kashif Ali', 'category' => 'marketing', 'exp' => '3 Years', 'phone' => '0300-1234506', 'email' => 'kashif.ali@example.com', 'salary' => 47000],
        ];

        $teachers = [];

        foreach ($definitions as $key => $row) {
            $teachers[$key] = Teacher::updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'exp' => $row['exp'],
                    'phone' => $row['phone'],
                    'category_id' => $categories[$row['category']]->id,
                    'status' => 1,
                    'salary' => $row['salary'],
                ]
            );
        }

        return $teachers;
    }

    /**
     * Students drive the whole dashboard, so the demo mixes all three
     * statuses (pending / confirmed / intership) and both class types.
     *
     * @param  array<string, Course>  $courses
     * @param  array<string, Teacher>  $teachers
     * @return array<string, Student> keyed by NIC
     */
    protected function seedStudents(array $courses, array $teachers): array
    {
        $definitions = [
            ['nic' => '35202-1234567-1', 'name' => 'Ali Hassan', 'father' => 'Muhammad Hassan', 'gender' => 1, 'city' => 'Lahore', 'address' => 'House 12, Street 4, Gulberg', 'phone' => '0321-1110001', 'email' => 'ali.hassan@example.com', 'course' => 'laravel', 'teacher' => 'ahmed', 'class' => 0, 'status' => 'confirmed'],
            ['nic' => '35202-1234567-2', 'name' => 'Fatima Noor', 'father' => 'Imran Noor', 'gender' => 0, 'city' => 'Lahore', 'address' => 'Flat 3, Model Town', 'phone' => '0321-1110002', 'email' => 'fatima.noor@example.com', 'course' => 'frontend', 'teacher' => 'bilal', 'class' => 1, 'status' => 'confirmed'],
            ['nic' => '35202-1234567-3', 'name' => 'Muhammad Bilal', 'father' => 'Shahid Khan', 'gender' => 1, 'city' => 'Faisalabad', 'address' => 'Peshawar Road', 'phone' => '0321-1110003', 'email' => 'm.bilal@example.com', 'course' => 'php', 'teacher' => 'bilal', 'class' => 0, 'status' => 'pending'],
            ['nic' => '35202-1234567-4', 'name' => 'Ayesha Siddiqui', 'father' => 'Kamran Siddiqui', 'gender' => 0, 'city' => 'Islamabad', 'address' => 'Sector F-11', 'phone' => '0321-1110004', 'email' => 'ayesha.s@example.com', 'course' => 'graphics', 'teacher' => 'sana', 'class' => 0, 'status' => 'confirmed'],
            ['nic' => '35202-1234567-5', 'name' => 'Zain Abbas', 'father' => 'Nadeem Abbas', 'gender' => 1, 'city' => 'Rawalpindi', 'address' => 'Bahria Town Phase 4', 'phone' => '0321-1110005', 'email' => 'zain.abbas@example.com', 'course' => 'video', 'teacher' => 'usman', 'class' => 1, 'status' => 'intership'],
            ['nic' => '35202-1234567-6', 'name' => 'Haris Raza', 'father' => 'Tanveer Raza', 'gender' => 1, 'city' => 'Multan', 'address' => 'Cantt Area', 'phone' => '0321-1110006', 'email' => 'haris.raza@example.com', 'course' => 'office', 'teacher' => 'hina', 'class' => 0, 'status' => 'confirmed'],
            ['nic' => '35202-1234567-7', 'name' => 'Maham Javed', 'father' => 'Javed Iqbal', 'gender' => 0, 'city' => 'Lahore', 'address' => 'Johar Town', 'phone' => '0321-1110007', 'email' => 'maham.javed@example.com', 'course' => 'marketing', 'teacher' => 'kashif', 'class' => 1, 'status' => 'pending'],
            ['nic' => '35202-1234567-8', 'name' => 'Usman Sheikh', 'father' => 'Kamran Sheikh', 'gender' => 1, 'city' => 'Gujranwala', 'address' => 'Model Town', 'phone' => '0321-1110008', 'email' => 'usman.sheikh@example.com', 'course' => 'laravel', 'teacher' => 'ahmed', 'class' => 1, 'status' => 'intership'],
            ['nic' => '35202-1234567-9', 'name' => 'Iqra Yousaf', 'father' => 'Yousaf Ahmed', 'gender' => 0, 'city' => 'Sialkot', 'address' => 'Cantt', 'phone' => '0321-1110009', 'email' => 'iqra.yousaf@example.com', 'course' => 'basic', 'teacher' => 'hina', 'class' => 0, 'status' => 'confirmed'],
            ['nic' => '35202-1234568-1', 'name' => 'Ahmed Yasin', 'father' => 'Nadeem Yasin', 'gender' => 1, 'city' => 'Lahore', 'address' => 'Shahdara', 'phone' => '0321-1110010', 'email' => 'ahmed.yasin@example.com', 'course' => 'php', 'teacher' => null, 'class' => 0, 'status' => 'pending'],
            ['nic' => '35202-1234568-2', 'name' => 'Sadia Rehman', 'father' => 'Abdur Rehman', 'gender' => 0, 'city' => 'Lahore', 'address' => 'Valencia', 'phone' => '0321-1110011', 'email' => 'sadia.rehman@example.com', 'course' => 'frontend', 'teacher' => 'bilal', 'class' => 1, 'status' => 'confirmed'],
            ['nic' => '35202-1234568-3', 'name' => 'Fahad Iqbal', 'father' => 'Iqbal Hussain', 'gender' => 1, 'city' => 'Quetta', 'address' => 'Civil Lines', 'phone' => '0321-1110012', 'email' => 'fahad.iqbal@example.com', 'course' => 'graphics', 'teacher' => 'sana', 'class' => 0, 'status' => 'pending'],
            ['nic' => '35202-1234568-4', 'name' => 'Rukhsana Bibi', 'father' => 'Muhammad Ramzan', 'gender' => 0, 'city' => 'Lahore', 'address' => 'Nisbat Town', 'phone' => '0321-1110013', 'email' => 'rukhsana.bibi@example.com', 'course' => 'office', 'teacher' => 'hina', 'class' => 0, 'status' => 'confirmed'],
            ['nic' => '35202-1234568-5', 'name' => 'Danish Ali', 'father' => 'Ali Raza', 'gender' => 1, 'city' => 'Hyderabad', 'address' => 'Auto Bhan Road', 'phone' => '0321-1110014', 'email' => 'danish.ali@example.com', 'course' => 'marketing', 'teacher' => 'kashif', 'class' => 1, 'status' => 'intership'],
            ['nic' => '35202-1234568-6', 'name' => 'Aiman Choudhry', 'father' => 'Kashif Choudhry', 'gender' => 0, 'city' => 'Lahore', 'address' => 'DHA Phase 6', 'phone' => '0321-1110015', 'email' => 'aiman.ch@example.com', 'course' => 'video', 'teacher' => 'usman', 'class' => 1, 'status' => 'pending'],
            ['nic' => '35202-1234568-7', 'name' => 'Waqas Nawaz', 'father' => 'Nawaz Sharif', 'gender' => 1, 'city' => 'Lahore', 'address' => 'Raiwind Road', 'phone' => '0321-1110016', 'email' => 'waqas.nawaz@example.com', 'course' => 'laravel', 'teacher' => 'ahmed', 'class' => 0, 'status' => 'confirmed'],
        ];

        $students = [];

        foreach ($definitions as $row) {
            $students[$row['nic']] = Student::updateOrCreate(
                ['nic' => $row['nic']],
                [
                    'name' => $row['name'],
                    'father' => $row['father'],
                    'gender' => $row['gender'],
                    'city' => $row['city'],
                    'address' => $row['address'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'course_id' => $courses[$row['course']]->id,
                    'teacher_id' => $row['teacher'] ? $teachers[$row['teacher']]->id : null,
                    'class' => $row['class'],
                    'status' => $row['status'],
                ]
            );
        }

        return $students;
    }

    /**
     * Payments are what the "Paid / Pending" box on the payment page sums up,
     * so most students are left partially paid on purpose.
     *
     * @param  array<string, Student>  $students
     */
    protected function seedPayments(array $students): void
    {
        $definitions = [
            ['nic' => '35202-1234567-1', 'amounts' => [15000, 15000]],
            ['nic' => '35202-1234567-2', 'amounts' => [20000]],
            ['nic' => '35202-1234567-4', 'amounts' => [10000, 10000]],
            ['nic' => '35202-1234567-6', 'amounts' => [6000, 6000]],
            ['nic' => '35202-1234567-9', 'amounts' => [8000]],
            ['nic' => '35202-1234568-2', 'amounts' => [18000, 20000]],
            ['nic' => '35202-1234568-3', 'amounts' => [15000]],
            ['nic' => '35202-1234568-7', 'amounts' => [45000]],
        ];

        foreach ($definitions as $row) {
            $student = $students[$row['nic']];

            foreach ($row['amounts'] as $amount) {
                Payment::firstOrCreate([
                    'student_id' => $student->id,
                    'amount' => $amount,
                ]);
            }
        }
    }

    /**
     * The dashboard groups expenses with MONTH(created_at) for the current
     * year, so the timestamps are backdated to make that chart/table useful.
     */
    protected function seedExpenses(): void
    {
        $year = Carbon::now()->year;

        $definitions = [
            ['expense' => 'Electricity Bill', 'amount' => 18000, 'description' => 'Monthly electricity bill for the computer lab', 'month' => 1],
            ['expense' => 'Internet Bill', 'amount' => 6500, 'description' => 'PTCL fibre line for the training floor', 'month' => 1],
            ['expense' => 'Stationery', 'amount' => 4500, 'description' => 'Whiteboard markers, registers and printing paper', 'month' => 2],
            ['expense' => 'Repair - Desks and Chairs', 'amount' => 12000, 'description' => 'Repainting and repairing student furniture', 'month' => 3],
            ['expense' => 'Marketing - Social Media Ads', 'amount' => 9000, 'description' => 'Facebook and Instagram ad campaign', 'month' => 4],
            ['expense' => 'Internet Bill', 'amount' => 6500, 'description' => 'PTCL fibre line for the training floor', 'month' => 5],
            ['expense' => 'Electricity Bill', 'amount' => 21500, 'description' => 'Summer peak month electricity bill', 'month' => 6],
            ['expense' => 'Equipment - Monitors', 'amount' => 45000, 'description' => 'Two new monitors and one keyboard set', 'month' => 7],
            ['expense' => 'Housekeeping', 'amount' => 5000, 'description' => 'Cleaning supplies and janitorial staff', 'month' => 8],
        ];

        foreach ($definitions as $row) {
            $month = min($row['month'], 12);

            // Skip an expense whose month has not happened yet this year.
            if ($month > (int) Carbon::now()->month) {
                continue;
            }

            $date = Carbon::create($year, $month, 12, 10, 0, 0);

            $expense = Expense::firstOrCreate([
                'expense' => $row['expense'],
                'amount' => $row['amount'],
                'description' => $row['description'],
            ]);

            if ($expense->created_at->year !== $year || $expense->created_at->month !== $month) {
                Expense::whereKey($expense->id)->update([
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
}