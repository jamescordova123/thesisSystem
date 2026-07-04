import AuthDiltrackLayout from '@/layouts/auth/auth-diltrack-layout';

export default function AuthLayout({
    children,
}: {
    title?: string;
    description?: string;
    children: React.ReactNode;
}) {
    return <AuthDiltrackLayout>{children}</AuthDiltrackLayout>;
}
