import { Link } from '@inertiajs/react';
import { ArrowRight, Megaphone } from 'lucide-react';
import type { PropsWithChildren } from 'react';
import { register } from '@/routes';

export default function AuthDiltrackLayout({ children }: PropsWithChildren) {
    return (
        <div className="font-diltrack-body auth-diltrack min-h-screen overflow-x-hidden bg-[#F8FAFC] text-[#0F172A] antialiased">
            <main className="flex min-h-screen w-full flex-grow flex-col md:flex-row">
                <div className="relative hidden overflow-hidden bg-[#4a0000] md:flex md:w-1/2 lg:w-3/5">
                    <img
                        alt="DILTC Students"
                        className="absolute inset-0 h-full w-full scale-105 object-cover"
                        src="/images/diltc/hero.png"
                    />
                    <div className="brand-gradient absolute inset-0" />
                    <div className="hero-mesh absolute inset-0 opacity-40" />

                    <div className="relative z-10 flex h-full flex-col justify-between p-12 text-white lg:p-20">
                        <div className="opacity-90">
                            <div className="mb-4 h-1 w-12 rounded-full bg-[#FFD700]" />
                            <span className="font-diltrack-body text-[11px] font-bold tracking-[0.2em] text-[#FFD700]/90 uppercase">
                                Institutional Excellence
                            </span>
                        </div>

                        <div className="max-w-xl">
                            <div className="font-diltrack-body mb-10 inline-flex animate-pulse items-center gap-2.5 rounded-full bg-[#FFD700] px-6 py-2.5 text-xs font-extrabold tracking-[0.05em] text-[#1A1A1A] uppercase shadow-xl">
                                <Megaphone className="h-4 w-4" />
                                Enrollment Now Open
                            </div>
                            <h2 className="font-diltrack-headline mb-8 text-[56px] leading-[1.02] font-extrabold tracking-[-0.03em] lg:text-[72px]">
                                Build your future at{' '}
                                <span className="text-[#FFD700] italic">DILTC.</span>
                            </h2>
                            <p className="font-diltrack-body mb-12 max-w-md text-xl leading-[1.6] font-medium text-white/90">
                                Empowering global citizens through high-standard language
                                proficiency and technological mastery.
                            </p>
                            <Link
                                href={register()}
                                className="font-diltrack-headline group inline-flex items-center gap-4 rounded-2xl bg-[#FFD700] px-12 py-5 text-[18px] font-bold tracking-tight text-[#1A1A1A] shadow-2xl transition-all duration-500 hover:scale-105 hover:bg-white hover:text-[#800000]"
                            >
                                Enroll Online Now
                                <ArrowRight className="h-5 w-5 font-bold transition-transform group-hover:translate-x-2" />
                            </Link>
                        </div>

                        <p className="text-[11px] font-medium tracking-widest text-white/40 uppercase">
                            Davao del Sur Institute Language and Training Center
                        </p>
                    </div>
                </div>

                <div className="relative flex w-full flex-col items-center justify-center overflow-y-auto bg-slate-50 p-6 md:w-1/2 md:p-12 lg:w-2/5 lg:p-16">
                    <div className="absolute top-0 right-0 -mt-32 -mr-32 h-64 w-64 rounded-full bg-[#800000]/5 blur-3xl" />
                    <div className="absolute bottom-0 left-0 -mb-32 -ml-32 h-64 w-64 rounded-full bg-[#FFD700]/10 blur-3xl" />
                    <div className="relative z-10 w-full max-w-md">{children}</div>
                </div>
            </main>
        </div>
    );
}
