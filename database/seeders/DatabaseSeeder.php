<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Notice;
use App\Models\Opportunity;
use App\Models\User;
use App\Support\Catalog;
use Illuminate\Database\Seeder;

/** All records are fictional demonstration data, not real announcements or statistics. */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $job = Catalog::IDENTITIES['Job seeker']['categories'];

        $resident = User::create([
            'name' => 'Demo Resident', 'email' => 'resident@example.invalid', 'password' => 'password',
            'phone' => '09171234567', 'barangay' => 'Poblacion', 'education' => 'High school graduate',
            'skills' => 'Customer service, basic computer use', 'identity' => 'Job seeker', 'interests' => $job,
        ]);
        User::create(['name' => 'Demo Admin', 'email' => 'admin@example.invalid', 'password' => 'password', 'is_admin' => true]);

        // [title, provider, category, location, deadline offset (days), posted offset, eligibility, description]
        $rows = [
            ['Municipal Office Clerk (Contractual)', 'Demo LGU Human Resource Office', 'Employment', 'Poblacion', 12, -5, 'Bauko resident, 18 or older, at least 2 years of college', 'Assist with records, filing, and front-desk service at the municipal hall. Sample posting for demonstration only.'],
            ['Highland Coffee Farm Technician', 'Sample Bauko Farmers Cooperative', 'Employment', 'Mabaay', 20, -2, 'Farming experience preferred, physically fit', 'Seasonal work covering pruning, harvest support, and post-harvest handling of Arabica coffee.'],
            ['Community Health Worker Aide', 'Demo Rural Health Unit', 'Employment', 'Abatan', -4, -30, 'Completed high school, willing to do barangay fieldwork', 'Support household visits and health information drives. This posting has already closed.'],
            ['Basic Welding and Fabrication Training', 'Sample TESDA-Style Training Center', 'Training and Skills Development', 'Poblacion', 9, -8, 'Out-of-school youth and adults, 18 to 45', 'A 4-week hands-on course with a certificate of completion. Tools provided.'],
            ['Digital Skills for Small Sellers', 'Demo PESO Bauko', 'Training and Skills Development', 'Municipality-wide', 25, -1, 'Residents with a smartphone, any age', 'Learn to photograph products, manage online orders, and track sales using free tools.'],
            ['Barangay Scholars Grant', 'Sample Municipal Scholarship Fund', 'Scholarships and Education', 'Municipality-wide', 30, -10, 'Incoming college students with a general average of 85 or higher', 'Tuition and book allowance for qualified students enrolled in a state college.'],
            ['Senior High Learning Support Allowance', 'Demo Education Assistance Desk', 'Scholarships and Education', 'Bila', 6, -14, 'Senior high school students from low-income households', 'Monthly allowance for transportation and school supplies.'],
            ['Vegetable Value-Adding Starter Kit', 'Demo Agriculture Office', 'Livelihood Programs', 'Sinto', 15, -3, 'Registered farmers or household food producers', 'Starter equipment and a short training on pickling and packaging local vegetables.'],
            ['Weaving Enterprise Support', 'Sample Women and Culture Council', 'Livelihood Programs', 'Monamon Norte', -10, -45, 'Weavers or learners, priority to women-led households', 'Materials and market linkage for traditional weaving groups. This round has closed.'],
            ['Emergency Rice and Grocery Assistance', 'Demo Social Welfare Office', 'Government Assistance', 'Municipality-wide', 4, -6, 'Households affected by recent weather disruptions', 'One-time food assistance for qualified households. Bring a valid ID to verify.'],
            ['Summer Youth Internship Program', 'Demo LGU Youth Council', 'Internships and Youth Programs', 'Poblacion', 18, -4, 'Students aged 16 to 24', 'Placement in municipal offices for hands-on experience in administration and community service.'],
            ['Youth Leadership Camp', 'Sample Bauko Youth Federation', 'Internships and Youth Programs', 'Abatan', 2, -12, 'Youth leaders aged 15 to 25', 'A weekend camp on community projects, public speaking, and disaster preparedness.'],
        ];
        $opps = [];
        foreach ($rows as $i => [$title, $provider, $cat, $loc, $dl, $posted, $elig, $desc]) {
            $o = Opportunity::create([
                'title' => $title, 'provider' => $provider, 'category' => $cat, 'location' => $loc,
                'deadline' => today()->addDays($dl), 'eligibility' => $elig, 'description' => $desc, 'status' => 'published',
            ]);
            $o->forceFill(['created_at' => now()->addDays($posted)])->save();
            $opps[$i + 1] = $o;
        }

        $apply = function (User $u, int $oppNo, string $status, int $ago) use ($opps) {
            $a = Application::create([
                'user_id' => $u->id, 'opportunity_id' => $opps[$oppNo]->id, 'name' => $u->name,
                'contact' => $u->phone ?: '09180000000', 'barangay' => $u->barangay ?: 'Abatan', 'status' => $status,
            ]);
            $a->forceFill(['created_at' => now()->subDays($ago)])->save();
        };

        foreach ([[1, 'Under Review', 4], [4, 'Shortlisted', 6], [6, 'Submitted', 1], [9, 'Rejected', 20], [8, 'Accepted', 9]] as [$n, $s, $ago]) {
            $apply($resident, $n, $s, $ago);
        }

        $others = [
            ['Ana Palangdan', 1, 'Shortlisted', 12], ['Ben Sagayo', 2, 'Accepted', 40], ['Carla Lacwasan', 4, 'Under Review', 8],
            ['Dino Balangeo', 5, 'Submitted', 2], ['Elena Pagaddut', 6, 'Shortlisted', 25], ['Felix Wadingan', 7, 'Accepted', 33],
            ['Grace Dulnuan', 10, 'Rejected', 3], ['Hector Maslang', 11, 'Under Review', 15], ['Ivy Cayat', 3, 'Rejected', 52],
            ['Jon Tamang', 9, 'Accepted', 70], ['Kara Bugtong', 12, 'Submitted', 5], ['Lito Fagyan', 8, 'Under Review', 95],
        ];
        foreach ($others as $k => [$name, $n, $s, $ago]) {
            $identity = array_keys(Catalog::IDENTITIES)[$k % 4];
            $u = User::create([
                'name' => $name, 'email' => 'resident'.($k + 2).'@example.invalid', 'password' => 'password',
                'barangay' => 'Abatan', 'identity' => $identity, 'interests' => Catalog::IDENTITIES[$identity]['categories'],
            ]);
            $apply($u, $n, $s, $ago);
        }

        Notice::create(['user_id' => $resident->id, 'kind' => 'deadline', 'text' => 'Reminder: Emergency Rice and Grocery Assistance closes in 4 days.']);
        Notice::create(['user_id' => $resident->id, 'kind' => 'deadline', 'text' => 'Reminder: Youth Leadership Camp closes in 2 days.']);
        Notice::create(['user_id' => $resident->id, 'kind' => 'status', 'text' => 'Your application for Basic Welding and Fabrication Training is now Shortlisted.', 'read_at' => now()]);
        Notice::create(['user_id' => $resident->id, 'kind' => 'new', 'text' => 'Matches your interests (Job seeker): Digital Skills for Small Sellers.', 'priority' => true, 'read_at' => now()]);
    }
}
