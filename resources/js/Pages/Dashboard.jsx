import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import StatsChart from './Components/StatsChart';
import ActionPieChart from './Components/ActionPieChart';
import ActionChart from './Components/ActionChart';
import FailureTable from './Components/FailureTable';
import { Head } from '@inertiajs/react';
import React, { useState } from 'react';
import FailureModal from './Components/FailureModal';



export default function Dashboard({ user, mappings = [], stats = {} }) {
    const [showFailures, setShowFailures] = useState(false);
    

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
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                    {user?.role === 'admin' && stats && (
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div className="bg-green-100 p-4 rounded shadow text-center">
                                <div className="text-lg font-semibold">✅ Success</div>
                                <div className="text-2xl font-bold">{stats.success || 0}</div>
                            </div>
                            <div
                                onClick={() => setShowFailures(true)}
                                className="bg-red-100 p-4 rounded shadow text-center cursor-pointer hover:bg-red-200 transition"
                            >
                                <div className="text-lg font-semibold">❌ Failed</div>
                                <div className="text-2xl font-bold">{stats.failed || 0}</div>
                            </div>
                            <div className="bg-blue-100 p-4 rounded shadow text-center">
                                <div className="text-lg font-semibold">🔁 Actions</div>
                                <div className="text-sm mt-1">
                                    {stats.by_action &&
                                        Object.entries(stats.by_action).map(([action, count]) => (
                                            <div key={action}>{action}: {count}</div>
                                        ))}
                                </div>
                            </div>
                        </div>
                    )}


                    {showFailures && user?.role === 'admin' && <FailureTable onClose={() => setShowFailures(false)} />}

                    {user?.role === 'admin' && <StatsChart />}
                    {user?.role === 'admin' && <ActionChart />}
                    {user?.role === 'admin' && <ActionPieChart />}



                    {user?.role === 'admin' && mappings && (
                        <div className="overflow-hidden bg-white shadow sm:rounded-lg">
                            <div className="p-6 text-gray-900">
                                <h3 className="text-lg font-bold mb-2">Recent Mappings</h3>
                                <div className="overflow-auto rounded shadow border">
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
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
