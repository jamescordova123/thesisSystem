import { Form, Head } from '@inertiajs/react';
import { CircleAlert, Eye, EyeOff, Lock, LogIn, Mail, Megaphone } from 'lucide-react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import PasskeyVerify from '@/components/passkey-verify';
import TeamInvitationAlert from '@/components/team-invitation-alert';
import TextLink from '@/components/text-link';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import type { TeamInvitationContext } from '@/types';

type Props = {
    status?: string;
    canResetPassword: boolean;
    teamInvitation?: TeamInvitationContext | null;
};

const rolePortals = ['ADMIN', 'REGISTRAR', 'CASHIER', 'STUDENT'] as const;

const inputClassName =
    'font-diltrack-body w-full rounded-2xl border border-[#E2E8F0]/60 bg-white/50 py-4.5 text-base font-medium shadow-sm outline-none transition-all duration-300 placeholder:text-slate-300 focus:border-[#800000] focus:bg-white focus:ring-[6px] focus:ring-[#800000]/5';

export default function Login({
    status,
    canResetPassword,
    teamInvitation,
}: Props) {
    const [showPassword, setShowPassword] = useState(false);

    return (
        <>
            <Head title="DILTrack Login - Academic Tracking System" />

            <div className="glass-panel relative rounded-[2.5rem] border border-white/50 p-8 shadow-premium md:p-12">
                <div className="mb-12 text-center md:text-left">
                    <div className="mb-10 flex flex-col items-center justify-center gap-5 md:flex-row md:justify-start">
                        <img
                            alt="DILTC Logo"
                            className="mx-auto h-20 w-auto object-contain drop-shadow-md md:mx-0"
                            src="/images/diltc/logo.png"
                        />
                        <div className="text-center md:text-left">
                            <h1 className="font-diltrack-headline text-[44px] leading-[0.9] font-extrabold tracking-[-0.05em] text-[#800000]">
                                DIL
                                <span className="font-normal text-[#0F172A]/40">Track</span>
                            </h1>
                            <p className="font-diltrack-body mt-1 text-[11px] font-bold tracking-[0.15em] text-[#475569] uppercase opacity-80">
                                Academic Management Portal
                            </p>
                        </div>
                    </div>
                    <div className="mb-8 hidden h-1.5 w-12 rounded-full bg-[#FFD700] md:block" />
                    <p className="font-diltrack-body text-lg font-semibold tracking-tight text-[#0F172A]">
                        Welcome back. Please sign in.
                    </p>
                </div>

                {teamInvitation && (
                    <div className="mb-6">
                        <TeamInvitationAlert invitation={teamInvitation} action="Log in" />
                    </div>
                )}

                <PasskeyVerify />

                {status && (
                    <div className="mb-6 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                        {status}
                    </div>
                )}

                <Form {...store.form()} resetOnSuccess={['password']} className="space-y-6">
                    {({ processing, errors }) => {
                        const hasCredentialsError = Boolean(errors.email || errors.password);

                        return (
                            <>
                                {hasCredentialsError && (
                                    <div className="flex items-center gap-3 rounded-2xl border border-red-100 bg-[#FEE2E2] px-4 py-4 text-[#991B1B]">
                                        <CircleAlert className="h-5 w-5 shrink-0" />
                                        <span className="text-sm font-bold">
                                            {errors.email ||
                                                errors.password ||
                                                'Invalid credentials. Please try again.'}
                                        </span>
                                    </div>
                                )}

                                <div className="space-y-2.5">
                                    <label
                                        className="font-diltrack-body px-1 text-[10px] font-extrabold tracking-[0.2em] text-[#475569] uppercase"
                                        htmlFor="email"
                                    >
                                        Institutional Email
                                    </label>
                                    <div className="group relative">
                                        <Mail className="absolute top-1/2 left-5 h-[22px] w-[22px] -translate-y-1/2 text-[#475569]/30 transition-colors duration-300 group-focus-within:text-[#800000]" />
                                        <input
                                            id="email"
                                            type="email"
                                            name="email"
                                            required
                                            autoFocus
                                            tabIndex={1}
                                            autoComplete="email"
                                            placeholder="username@diltc.edu.ph"
                                            className={`${inputClassName} pl-14 pr-5`}
                                        />
                                    </div>
                                    <InputError message={errors.email} />
                                </div>

                                <div className="space-y-2.5">
                                    <div className="flex items-end justify-between px-1">
                                        <label
                                            className="font-diltrack-body text-[10px] font-extrabold tracking-[0.2em] text-[#475569] uppercase"
                                            htmlFor="password"
                                        >
                                            Security Password
                                        </label>
                                        {canResetPassword && (
                                            <TextLink
                                                href={request()}
                                                className="text-[11px] font-bold text-[#800000] hover:underline"
                                                tabIndex={5}
                                            >
                                                Forgot password?
                                            </TextLink>
                                        )}
                                    </div>
                                    <div className="group relative">
                                        <Lock className="absolute top-1/2 left-5 h-[22px] w-[22px] -translate-y-1/2 text-[#475569]/30 transition-colors duration-300 group-focus-within:text-[#800000]" />
                                        <input
                                            id="password"
                                            type={showPassword ? 'text' : 'password'}
                                            name="password"
                                            required
                                            tabIndex={2}
                                            autoComplete="current-password"
                                            placeholder="••••••••"
                                            className={`${inputClassName} pl-14 pr-14`}
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPassword((prev) => !prev)}
                                            className="absolute top-1/2 right-5 -translate-y-1/2 p-1 text-[#475569]/40 transition-colors hover:text-[#800000]"
                                            aria-label={
                                                showPassword ? 'Hide password' : 'Show password'
                                            }
                                            tabIndex={-1}
                                        >
                                            {showPassword ? (
                                                <EyeOff className="h-5 w-5" />
                                            ) : (
                                                <Eye className="h-5 w-5" />
                                            )}
                                        </button>
                                    </div>
                                    <InputError message={errors.password} />
                                </div>

                                <div className="flex items-center px-1">
                                    <label className="group flex cursor-pointer items-center gap-3">
                                        <input
                                            type="checkbox"
                                            name="remember"
                                            tabIndex={3}
                                            className="h-5 w-5 cursor-pointer rounded-md border-[#E2E8F0] text-[#800000] transition-all focus:ring-[#800000]/20"
                                        />
                                        <span className="font-diltrack-body text-sm font-semibold text-[#475569] transition-colors group-hover:text-[#800000]">
                                            Remember my session
                                        </span>
                                    </label>
                                </div>

                                <button
                                    type="submit"
                                    disabled={processing}
                                    tabIndex={4}
                                    data-test="login-button"
                                    className="font-diltrack-headline mt-6 flex w-full items-center justify-center gap-3 rounded-2xl bg-[#800000] py-5 text-[17px] font-bold tracking-tight text-white shadow-brand transition-all duration-500 hover:bg-[#4a0000] active:scale-[0.98] disabled:opacity-70"
                                >
                                    {processing ? (
                                        <Spinner />
                                    ) : (
                                        <>
                                            Sign In to Dashboard
                                            <LogIn className="h-5 w-5" />
                                        </>
                                    )}
                                </button>
                            </>
                        );
                    }}
                </Form>

                <div className="mt-14 border-t border-slate-100/80 pt-10">
                    <span className="font-diltrack-body mb-6 block text-center text-[9px] font-bold tracking-[0.3em] text-slate-400 uppercase">
                        Authorized Portals
                    </span>
                    <div className="flex flex-wrap justify-center gap-2.5">
                        {rolePortals.map((role) => (
                            <span
                                key={role}
                                className={`font-diltrack-body rounded-xl px-4 py-2 text-[10px] font-bold tracking-wider uppercase ${
                                    role === 'STUDENT'
                                        ? 'border border-[#800000]/20 bg-[#800000]/5 text-[#800000] shadow-sm ring-2 ring-[#800000]/5'
                                        : 'bg-slate-100/50 text-slate-500'
                                }`}
                            >
                                {role}
                            </span>
                        ))}
                    </div>
                </div>
            </div>

            <div className="mt-12 flex w-full max-w-md flex-col items-center gap-6 border-t border-slate-100 pt-8 md:hidden">
                <div className="font-diltrack-body inline-flex items-center gap-2 rounded-full bg-[#FFD700] px-6 py-2 text-xs font-bold text-[#1A1A1A] shadow-md">
                    <Megaphone className="h-4 w-4" />
                    Enrollment is Open
                </div>
                <p className="max-w-[240px] text-center text-[11px] leading-relaxed font-medium text-slate-400">
                    © {new Date().getFullYear()} Davao del Sur Institute Language and Training
                    Center. All Rights Reserved.
                </p>
            </div>
        </>
    );
}
