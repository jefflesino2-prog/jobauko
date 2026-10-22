<?php

namespace App\Support;

class Catalog
{
    public const CATEGORIES = [
        'Employment', 'Training and Skills Development', 'Scholarships and Education',
        'Livelihood Programs', 'Government Assistance', 'Internships and Youth Programs',
    ];

    public const LOCATIONS = ['Municipality-wide', 'Poblacion', 'Abatan', 'Mabaay', 'Bila', 'Sinto', 'Monamon Norte'];

    public const STATUSES = ['Submitted', 'Under Review', 'Shortlisted', 'Accepted', 'Rejected'];

    public const IDENTITIES = [
        'Job seeker' => ['blurb' => 'Looking for work or skills training', 'categories' => ['Employment', 'Training and Skills Development']],
        'Student' => ['blurb' => 'In school or planning for college', 'categories' => ['Scholarships and Education', 'Internships and Youth Programs']],
        'Farmer or small business owner' => ['blurb' => 'Growing or selling local products', 'categories' => ['Livelihood Programs', 'Training and Skills Development']],
        'Household needing assistance' => ['blurb' => 'Looking for government aid and support', 'categories' => ['Government Assistance', 'Livelihood Programs']],
    ];

    public const ELIGIBILITY_PRESETS = [
        'Jobs & Occupations' => [
            'Administrative & Office Clerk',
            'Farm Technician / Agricultural Worker',
            'Community Health Worker Aide',
            'Welder & Metal Fabricator',
            'Micro-Retailer & Online Seller',
            'Traditional Weaver & Artisan',
            'Food Processor & Baker',
            'Carpentry & Construction Worker',
            'Youth Leader & Community Organizer',
            'Customer Service & Sales Associate',
        ],
        'Skills & Abilities' => [
            'Customer service & communication',
            'Basic computer & digital tools',
            'Smartphone & social media selling',
            'Farming & crop care experience',
            'Handicrafts & traditional weaving',
            'Welding & equipment handling',
            'Physically fit for field labor',
            'Public speaking & event assistance',
            'Bookkeeping & inventory recording',
            'Food preparation & hygiene',
        ],
        'Target Groups & Residency' => [
            'Bauko resident (18 or older)',
            'Job seekers & unemployed adults',
            'High school & college students',
            'Out-of-school youth (OSY)',
            'Farmers & food growers',
            'Small business / micro-entrepreneurs',
            'Women-led households',
            'Indigent / low-income families',
            'Persons with Disabilities (PWD)',
            'Senior citizens',
        ],
        'Education & Qualifications' => [
            'At least 2 years of college',
            'College graduate / diploma holder',
            'High school / Senior high graduate',
            'Enrolled college student (GWA 85%+)',
            'Completed TVET / TESDA course',
            'With valid government / Barangay ID',
            'No formal education required',
        ],
    ];

    public static function barangays(): array
    {
        return array_values(array_filter(self::LOCATIONS, fn ($l) => $l !== 'Municipality-wide'));
    }
}
