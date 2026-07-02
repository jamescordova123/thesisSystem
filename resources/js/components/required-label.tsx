import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

export default function RequiredLabel({
    htmlFor,
    children,
    className,
}: {
    htmlFor: string;
    children: React.ReactNode;
    className?: string;
}) {
    return (
        <Label htmlFor={htmlFor} className={cn(className)}>
            {children}{' '}
            <span className="text-red-600 dark:text-red-400" aria-hidden="true">
                *
            </span>
        </Label>
    );
}
