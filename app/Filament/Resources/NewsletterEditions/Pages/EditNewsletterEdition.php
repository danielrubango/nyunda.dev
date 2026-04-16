<?php

namespace App\Filament\Resources\NewsletterEditions\Pages;

use App\Actions\Newsletter\SendNewsletterEdition;
use App\Filament\Resources\NewsletterEditions\NewsletterEditionResource;
use App\Models\NewsletterEdition;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Throwable;

class EditNewsletterEdition extends EditRecord
{
    protected static string $resource = NewsletterEditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send')
                ->label('Envoyer la newsletter')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Envoyer cette édition ?')
                ->modalDescription('Cette action enverra un email à tous les abonnés confirmés. Elle ne peut pas être annulée.')
                ->modalSubmitActionLabel('Oui, envoyer')
                ->visible(fn (NewsletterEdition $record): bool => $record->isDraft())
                ->action(function (NewsletterEdition $record): void {
                    try {
                        app(SendNewsletterEdition::class)->handle($record);
                        Notification::make()
                            ->title('Newsletter en cours d\'envoi')
                            ->body("Envoi démarré pour {$record->recipients_count} abonnés.")
                            ->success()
                            ->send();
                        $this->redirect($this->getUrl(['record' => $record]));
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Erreur lors de l\'envoi')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            DeleteAction::make()
                ->visible(fn (NewsletterEdition $record): bool => $record->isDraft()),
        ];
    }
}
