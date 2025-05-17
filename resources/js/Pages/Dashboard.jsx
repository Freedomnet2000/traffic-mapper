import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ user, mappings = [] }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            You're logged in!
                            <br />
                                {user?.role === 'admin' && mappings && (
                                    <div className="mt-6 overflow-auto rounded shadow border">
                                        <h3 className="text-lg font-bold mb-2">Recent Mappings</h3>
                                        <table className="min-w-full divide-y divide-gray-200 text-sm">
                                            <thead className="bg-gray-100">
                                                <tr>
                                                    <th className="px-4 py-2 text-left">Keyword</th>
                                                    <th className="px-4 py-2 text-left">Src</th>
                                                    <th className="px-4 py-2 text-left">Creative</th>
                                                    <th className="px-4 py-2 text-left">Our Param</th>
                                                    <th className="px-4 py-2 text-left">Created</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-100">
                                                {mappings.data.map((map) => (
                                                    <tr key={map.our_param}>
                                                        <td className="px-4 py-2">{map.keyword}</td>
                                                        <td className="px-4 py-2">{map.src}</td>
                                                        <td className="px-4 py-2">{map.creative}</td>
                                                        <td className="px-4 py-2 font-mono">{map.our_param}</td>
                                                        <td className="px-4 py-2">{new Date(map.created_at).toLocaleString()}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
