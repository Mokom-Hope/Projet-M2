<?php

namespace App\Http\Controllers\Immo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bien;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class ImmoController extends Controller
{
    /**
     * Traite les messages du chatbot immobilier
     */
    public function processMessage(Request $request)
    {
        $message = trim($request->input('message'));
        $name = $request->input('name', 'Client');
        $sessionId = $request->input('session_id', md5($request->ip()));
        
        // Récupérer l'historique de conversation
        $conversationHistory = $this->getConversationHistory($sessionId);
        
        // Ajouter le message utilisateur à l'historique
        $conversationHistory[] = [
            'role' => 'user',
            'content' => $message
        ];
        
        // Sauvegarder l'historique mis à jour
        $this->saveConversationHistory($sessionId, $conversationHistory);
        
        // Vérifier si c'est une présentation de nom
        if ($this->isNameIntroduction($message)) {
            // Extraire le nom
            $nameMatch = preg_match("/(?:je m['\"]appelle|moi c['\"]est|je suis)\s+(\w+)/i", $message, $matches);
            if ($nameMatch && isset($matches[1])) {
                $extractedName = ucfirst(strtolower($matches[1]));
                $response = "Enchanté $extractedName ! Comment puis-je vous aider aujourd'hui ? Vous cherchez à acheter, louer ou investir dans l'immobilier ?";
                
                // Ajouter la réponse à l'historique
                $this->addResponseToHistory($sessionId, $response);
                
                return response()->json(['reply' => $response]);
            }
        }
        
        // Vérifier si c'est une question d'identité ou une salutation
        if ($this->isIdentityQuestion($message)) {
            $response = "Bonjour ! Je suis Immo, votre assistant immobilier virtuel. Je peux vous aider à trouver des biens immobiliers, vous donner des conseils sur l'investissement, et répondre à vos questions sur le marché immobilier au Cameroun. Comment puis-je vous aider aujourd'hui ?";
            
            // Ajouter la réponse à l'historique
            $this->addResponseToHistory($sessionId, $response);
            
            return response()->json(['reply' => $response]);
        }
        
        // Vérifier si c'est une question de suivi sur un bien précédemment mentionné
        if ($this->isPropertyFollowUpQuestion($message, $conversationHistory)) {
            return $this->handlePropertyFollowUp($message, $conversationHistory, $name, $sessionId);
        }
        
        // Si c'est une salutation ou une question sur l'identité du bot
        if ($this->isGreetingOrIdentityQuestion($message)) {
            $response = "Bonjour ! Je suis Immo, votre assistant immobilier virtuel. Je peux vous aider à trouver des biens immobiliers, vous donner des conseils sur l'investissement, et répondre à vos questions sur le marché immobilier au Cameroun. Comment puis-je vous aider aujourd'hui ?";
            
            // Ajouter la réponse à l'historique
            $this->addResponseToHistory($sessionId, $response);
            
            return response()->json(['reply' => $response]);
        }

        // Vérifier si c'est une question générale sur l'immobilier
        if ($this->isGeneralRealEstateQuestion($message)) {
            $response = $this->handleGeneralQuestion($message, $name, $sessionId, $conversationHistory);
            
            // Ajouter la réponse à l'historique
            $this->addResponseToHistory($sessionId, $response);
            
            return response()->json(['reply' => $response]);
        }
        
        // Analyse du message pour extraire les informations clés
        $extractedInfo = $this->extractInformation($message);
        
        // Recherche de biens correspondants
        $properties = $this->findMatchingProperties($extractedInfo);
        
        // Si des biens sont trouvés, construire la réponse avec les biens
        if (!$properties->isEmpty()) {
            // Stocker les biens trouvés dans la session pour les questions de suivi
            $this->storeFoundProperties($sessionId, $properties);
            
            $response = $this->buildResponse($properties, $extractedInfo, $name, $sessionId);
            
            // Ajouter la réponse à l'historique
            $this->addResponseToHistory($sessionId, $response);
            
            return response()->json(['reply' => $response]);
        }
        
        // Si aucun bien n'est trouvé mais que c'est une demande de bien
        if ($this->isPropertyRequest($message)) {
            // Suggérer des alternatives
            $suggestions = $this->suggestAlternatives($extractedInfo);
            $response = "Je n'ai pas trouvé de biens correspondant exactement à vos critères. " . $suggestions;
            
            // Ajouter la réponse à l'historique
            $this->addResponseToHistory($sessionId, $response);
            
            return response()->json(['reply' => $response]);
        }
        
        // Si ce n'est ni une question générale ni une demande de bien reconnue
        // Essayer de donner une réponse pertinente basée sur le contenu du message
        $response = $this->handleGeneralQuestion($message, $name, $sessionId, $conversationHistory);
        
        // Ajouter la réponse à l'historique
        $this->addResponseToHistory($sessionId, $response);
        
        return response()->json(['reply' => $response]);
    }
    
    /**
     * Vérifie si le message est une présentation de nom
     */
    private function isNameIntroduction($message)
    {
        $lowerMessage = strtolower($message);
        
        // Patterns pour les présentations de nom
        $namePatterns = [
            '/je m[\'"]appelle\s+\w+/i',
            '/moi c[\'"]est\s+\w+/i',
            '/je suis\s+\w+/i',
            '/mon nom est\s+\w+/i',
            '/appelle[- ]moi\s+\w+/i'
        ];
        
        
        foreach ($namePatterns as $pattern) {
            if (preg_match($pattern, $lowerMessage)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si le message est une question d'identité
     */
    private function isIdentityQuestion($message)
    {
        $lowerMessage = strtolower($message);
        
        // Patterns spécifiques pour les questions d'identité
        $identityPatterns = [
            '/comment t[\'"]appell?e[sz]?[ -]tu/i',
            '/comment tu t[\'"]appell?e[sz]?/i',
            '/c[\'"]est quoi ton nom/i',
            '/quel est ton nom/i',
            '/tu t[\'"]appell?e[sz]?/i',
            '/ton nom/i',
            '/qui es[ -]tu/i',
            '/tu es qui/i',
            '/t[\'"]es qui/i',
            '/qui êtes[ -]vous/i',
            '/comment vous appell?e[sz]?[ -]vous/i',
            '/votre nom/i'
        ];
        
        
        foreach ($identityPatterns as $pattern) {
            if (preg_match($pattern, $lowerMessage)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si le message est une question de suivi sur un bien précédemment mentionné
     */
    private function isPropertyFollowUpQuestion($message, $conversationHistory)
    {
        $lowerMessage = strtolower($message);
        
        // Mots-clés indiquant une question de suivi sur un bien
        $followUpKeywords = [
            'pourquoi', 'cette maison', 'ce bien', 'cette propriété', 'ce terrain',
            'plus de détails', 'plus d\'informations', 'caractéristiques', 'avantages',
            'inconvénients', 'intéressant', 'intéressante', 'photos', 'visite',
            'adresse', 'localisation', 'emplacement', 'quartier', 'voisinage',
            'négocier', 'négociation', 'offre', 'proposition', 'réduction',
            'rabais', 'prix', 'coût', 'superficie', 'surface', 'dimensions',
            'pièces', 'chambres', 'salles de bain', 'jardin', 'garage',
            'parking', 'sécurité', 'titre foncier', 'papiers', 'documents'
        ];
        
        // Vérifier si le message contient des mots-clés de suivi
        $containsFollowUpKeyword = false;
        foreach ($followUpKeywords as $keyword) {
            if (strpos($lowerMessage, $keyword) !== false) {
                $containsFollowUpKeyword = true;
                break;
            }
        }
        
        if (!$containsFollowUpKeyword) {
            return false;
        }
        
        // Vérifier si un bien a été mentionné dans les messages précédents
        $propertyMentioned = false;
        foreach (array_reverse($conversationHistory) as $message) {
            if ($message['role'] === 'assistant' && 
                (strpos($message['content'], 'bien qui pourrait vous intéresser') !== false ||
                 strpos($message['content'], 'biens qui pourraient vous intéresser') !== false)) {
                $propertyMentioned = true;
                break;
            }
        }
        
        return $propertyMentioned;
    }
    
    /**
     * Gère les questions de suivi sur un bien précédemment mentionné
     */
    private function handlePropertyFollowUp($message, $conversationHistory, $name, $sessionId)
    {
        // Récupérer les biens précédemment trouvés
        $properties = $this->getStoredProperties($sessionId);
        
        if (empty($properties)) {
            $response = "Je suis désolé, mais je ne retrouve pas les détails du bien dont nous parlions. Pourriez-vous me préciser quel type de bien vous intéresse ?";
            $this->addResponseToHistory($sessionId, $response);
            return response()->json(['reply' => $response]);
        }
        
        $lowerMessage = strtolower($message);
        
        // Déterminer le type de question de suivi
        if (strpos($lowerMessage, 'pourquoi') !== false || 
            strpos($lowerMessage, 'intéressant') !== false || 
            strpos($lowerMessage, 'avantages') !== false) {
            
            // Question sur les avantages du bien
            $property = $properties[0]; // Prendre le premier bien (le plus pertinent)
            
            $response = $this->generatePropertyAdvantagesResponse($property);
            $this->addResponseToHistory($sessionId, $response);
            return response()->json(['reply' => $response]);
            
        } elseif (strpos($lowerMessage, 'prix') !== false || 
                 strpos($lowerMessage, 'coût') !== false || 
                 strpos($lowerMessage, 'négocier') !== false) {
            
            // Question sur le prix
            $property = $properties[0];
            
            $response = $this->generatePriceAnalysisResponse($property);
            $this->addResponseToHistory($sessionId, $response);
            return response()->json(['reply' => $response]);
            
        } elseif (strpos($lowerMessage, 'quartier') !== false || 
                 strpos($lowerMessage, 'localisation') !== false || 
                 strpos($lowerMessage, 'emplacement') !== false || 
                 strpos($lowerMessage, 'adresse') !== false) {
            
            // Question sur l'emplacement
            $property = $properties[0];
            
            $response = $this->generateLocationInfoResponse($property);
            $this->addResponseToHistory($sessionId, $response);
            return response()->json(['reply' => $response]);
            
        } elseif (strpos($lowerMessage, 'visite') !== false) {
            
            // Question sur la visite
            $property = $properties[0];
            $propertyUrl = route('properties.show', ['id' => $property->id]);
            
            $response = "Je serais ravi d'organiser une visite pour cette propriété ! Vous pouvez choisir entre une visite virtuelle en ligne ou une visite physique avec l'un de nos agents. Pour planifier votre visite, cliquez simplement sur ce lien : <a href='{$propertyUrl}' target='_blank' class='text-indigo-600 underline'>Organiser une visite</a>. Nous sommes disponibles 7j/7 et nous nous adapterons à votre emploi du temps.";
            $this->addResponseToHistory($sessionId, $response);
            return response()->json(['reply' => $response]);
            
        } else {
            // Question générale sur le bien
            $property = $properties[0];
            
            $response = $this->generateDetailedPropertyInfo($property);
            $this->addResponseToHistory($sessionId, $response);
            return response()->json(['reply' => $response]);
        }
    }
    
    /**
     * Génère une réponse détaillée sur les avantages d'un bien
     */
    private function generatePropertyAdvantagesResponse($property)
    {
        $response = "Cette propriété présente plusieurs avantages qui en font une excellente opportunité :<br><br>";
        
        // Avantages généraux
        $response .= "<strong>Rapport qualité-prix exceptionnel :</strong> À " . number_format($property->prix, 0, ',', ' ') . " FCFA pour " . $property->superficie . " m², le prix au m² est très compétitif pour le marché actuel.<br><br>";
        
        // Avantages spécifiques selon le type
        switch ($property->type) {
            case 'Maison':
                $response .= "<strong>Avantages spécifiques :</strong><ul class='list-disc pl-5 my-2'>";
                $response .= "<li>Emplacement stratégique dans un quartier recherché avec une bonne valorisation immobilière</li>";
                $response .= "<li>Sécurité 24h/24 avec gardiennage et système d'alarme</li>";
                $response .= "<li>Construction récente avec des matériaux de qualité</li>";
                $response .= "<li>Proximité des écoles, commerces et services essentiels</li>";
                $response .= "<li>Facilité d'accès aux axes routiers principaux</li>";
                $response .= "</ul>";
                break;
                
            case 'Terrain':
                $response .= "<strong>Avantages spécifiques :</strong><ul class='list-disc pl-5 my-2'>";
                $response .= "<li>Terrain plat, facile à construire sans travaux de terrassement coûteux</li>";
                $response .= "<li>Titre foncier disponible et à jour, garantissant la sécurité juridique</li>";
                $response .= "<li>Orientation idéale pour l'ensoleillement</li>";
                $response .= "<li>Zone en développement avec forte appréciation de la valeur</li>";
                $response .= "<li>Accès aux réseaux d'eau et d'électricité à proximité</li>";
                $response .= "</ul>";
                break;
                
            case 'LocalCommercial':
                $response .= "<strong>Avantages spécifiques :</strong><ul class='list-disc pl-5 my-2'>";
                $response .= "<li>Fort passage commercial garantissant une visibilité maximale</li>";
                $response .= "<li>Emplacement en façade sur une artère principale</li>";
                $response .= "<li>Potentiel de rendement locatif élevé (8-12% annuel)</li>";
                $response .= "<li>Espace modulable selon vos besoins commerciaux</li>";
                $response .= "<li>Stationnement disponible pour la clientèle</li>";
                $response .= "</ul>";
                break;
                
            default:
                $response .= "<strong>Avantages spécifiques :</strong><ul class='list-disc pl-5 my-2'>";
                $response .= "<li>Excellent rapport qualité-prix pour ce type de bien</li>";
                $response .= "<li>Emplacement stratégique dans un quartier en développement</li>";
                $response .= "<li>Potentiel d'appréciation de la valeur dans les prochaines années</li>";
                $response .= "</ul>";
        }
        
        $response .= "<br>Souhaitez-vous en savoir plus sur un aspect particulier de cette propriété ?";
        
        return $response;
    }
    
    /**
     * Génère une analyse du prix d'un bien
     */
    private function generatePriceAnalysisResponse($property)
    {
        $pricePerSqm = $property->prix / $property->superficie;
        
        $response = "<strong>Analyse du prix de cette propriété :</strong><br><br>";
        
        $response .= "Prix total : " . number_format($property->prix, 0, ',', ' ') . " FCFA<br>";
        $response .= "Superficie : " . $property->superficie . " m²<br>";
        $response .= "Prix au m² : " . number_format($pricePerSqm, 0, ',', ' ') . " FCFA/m²<br><br>";
        
        // Analyse comparative selon le type et l'emplacement
        switch ($property->type) {
            case 'Maison':
                $response .= "Pour une maison dans ce secteur, le prix moyen au m² se situe généralement entre " . number_format($pricePerSqm * 0.9, 0, ',', ' ') . " et " . number_format($pricePerSqm * 1.2, 0, ',', ' ') . " FCFA/m². ";
                
                if (strpos(strtolower($property->adresse), 'bastos') !== false || 
                    strpos(strtolower($property->adresse), 'golf') !== false) {
                    $response .= "Dans un quartier premium comme celui-ci, ce prix est tout à fait justifié par la qualité de l'environnement et des prestations.";
                } else {
                    $response .= "Ce prix est compétitif pour le marché actuel, offrant un bon équilibre entre qualité et investissement.";
                }
                break;
                
            case 'Terrain':
                $response .= "Pour un terrain dans cette zone, le prix moyen au m² varie entre " . number_format($pricePerSqm * 0.8, 0, ',', ' ') . " et " . number_format($pricePerSqm * 1.3, 0, ',', ' ') . " FCFA/m². ";
                $response .= "La présence d'un titre foncier et l'accès aux réseaux (eau, électricité) justifient généralement un prix dans cette fourchette.";
                break;
                
            default:
                $response .= "Pour ce type de bien dans ce secteur, ce prix est aligné avec les tendances actuelles du marché.";
        }
        
        $response .= "<br><br><strong>Possibilités de négociation :</strong><br>";
        $response .= "Une marge de négociation de 5 à 10% est généralement possible, selon la durée de mise en vente et la motivation du vendeur. Je peux vous accompagner dans cette négociation pour obtenir les meilleures conditions.";
        
        $response .= "<br><br><strong>Frais additionnels à prévoir :</strong><ul class='list-disc pl-5 my-2'>";
        $response .= "<li>Frais de notaire : environ 7-9% du prix d'achat</li>";
        $response .= "<li>Frais d'enregistrement et taxes : 2-3% du prix d'achat</li>";
        if ($property->type === 'Terrain') {
            $response .= "<li>Frais de bornage si nécessaire : 200 000 - 500 000 FCFA</li>";
        }
        $response .= "</ul>";
        
        $response .= "<br>Souhaitez-vous que je vous aide à planifier une visite ou à formuler une offre pour ce bien ?";
        
        return $response;
    }
    
    /**
     * Génère des informations sur l'emplacement d'un bien
     */
    private function generateLocationInfoResponse($property)
    {
        $response = "<strong>Informations sur l'emplacement de cette propriété :</strong><br><br>";
        
        // Extraire le quartier ou la ville de l'adresse
        $location = $property->adresse;
        $locationInfo = "";
        
        // Informations sur les quartiers connus
        if (strpos(strtolower($location), 'bastos') !== false) {
            $locationInfo = "Bastos est l'un des quartiers les plus prestigieux de Yaoundé, prisé pour son calme, sa sécurité et la présence de nombreuses ambassades. C'est un secteur résidentiel haut de gamme avec d'excellentes infrastructures.";
        } elseif (strpos(strtolower($location), 'golf') !== false) {
            $locationInfo = "Le quartier du Golf est une zone résidentielle de standing à Yaoundé, caractérisée par de belles propriétés, un environnement verdoyant et un cadre de vie paisible.";
        } elseif (strpos(strtolower($location), 'akwa') !== false) {
            $locationInfo = "Akwa est le cœur commercial de Douala, offrant une excellente visibilité et un fort potentiel commercial. C'est un quartier dynamique avec de nombreux commerces et services.";
        } elseif (strpos(strtolower($location), 'bonanjo') !== false) {
            $locationInfo = "Bonanjo est le quartier administratif et d'affaires de Douala, abritant de nombreux sièges d'entreprises et institutions. C'est un secteur prestigieux avec une forte valeur immobilière.";
        } elseif (strpos(strtolower($location), 'bonapriso') !== false) {
            $locationInfo = "Bonapriso est un quartier résidentiel haut de gamme de Douala, apprécié pour son calme, sa sécurité et sa proximité avec le centre-ville.";
        } elseif (strpos(strtolower($location), 'kribi') !== false) {
            $locationInfo = "Kribi est une ville balnéaire en plein essor grâce à son port en eau profonde. C'est une destination touristique prisée offrant d'excellentes opportunités d'investissement immobilier.";
        } elseif (strpos(strtolower($location), 'yaoundé') !== false || strpos(strtolower($location), 'yaounde') !== false) {
            $locationInfo = "Yaoundé, capitale politique du Cameroun, est une ville en constante expansion avec un marché immobilier dynamique. Elle offre un cadre de vie agréable avec son relief vallonné et son climat tempéré.";
        } elseif (strpos(strtolower($location), 'douala') !== false) {
            $locationInfo = "Douala, capitale économique du Cameroun, est le centre des affaires du pays. Son marché immobilier est l'un des plus dynamiques d'Afrique centrale, porté par une forte demande.";
        } else {
            $locationInfo = "Cette propriété est située dans un emplacement stratégique offrant un bon équilibre entre accessibilité et cadre de vie agréable.";
        }
        
        $response .= "Adresse : " . $property->adresse . "<br><br>";
        $response .= $locationInfo . "<br><br>";
        
        $response .= "<strong>Proximité et commodités :</strong><ul class='list-disc pl-5 my-2'>";
        $response .= "<li>Accès aux transports : Facilement accessible par les axes routiers principaux</li>";
        $response .= "<li>Commerces : Supermarchés et boutiques à proximité</li>";
        $response .= "<li>Éducation : Écoles et établissements d'enseignement dans un rayon de 2-3 km</li>";
        $response .= "<li>Santé : Centres médicaux et pharmacies accessibles</li>";
        $response .= "<li>Loisirs : Restaurants et espaces de détente à proximité</li>";
        $response .= "</ul>";
        
        $response .= "<br>Souhaitez-vous organiser une visite pour découvrir cet emplacement par vous-même ?";
        
        return $response;
    }
    
    /**
     * Génère des informations détaillées sur un bien
     */
    private function generateDetailedPropertyInfo($property)
    {
        $response = "<strong>Informations détaillées sur cette propriété :</strong><br><br>";
        
        $response .= "<div class='bg-gray-100 p-3 rounded-lg mb-3'>";
        $response .= "<strong class='text-lg'>" . $property->titre . "</strong> (" . $property->type . ")<br>";
        $response .= "<span class='text-gray-700'>Prix : " . number_format($property->prix, 0, ',', ' ') . " FCFA | Superficie : " . $property->superficie . " m²</span><br>";
        $response .= "<span class='text-gray-700'>Adresse : " . $property->adresse . "</span>";
        $response .= "</div>";
        
        $response .= "<strong>Description :</strong><br>";
        $response .= $property->description . "<br><br>";
        
        // Caractéristiques spécifiques selon le type
        $response .= "<strong>Caractéristiques :</strong><ul class='list-disc pl-5 my-2'>";
        
        switch ($property->type) {
            case 'Maison':
                $response .= "<li>Type de construction : Construction en dur avec finitions de qualité</li>";
                $response .= "<li>Nombre de pièces : Plusieurs chambres et espaces de vie</li>";
                $response .= "<li>Extérieur : Jardin aménagé et espace de stationnement</li>";
                $response .= "<li>Sécurité : Système d'alarme et gardiennage</li>";
                break;
                
            case 'Terrain':
                $response .= "<li>Topographie : Terrain plat, facile à aménager</li>";
                $response .= "<li>Statut juridique : Titre foncier disponible</li>";
                $response .= "<li>Viabilisation : Accès aux réseaux d'eau et d'électricité</li>";
                $response .= "<li>Potentiel : Idéal pour construction résidentielle ou commerciale</li>";
                break;
                
            case 'LocalCommercial':
                $response .= "<li>Visibilité : Emplacement commercial stratégique</li>";
                $response .= "<li>Agencement : Espace modulable selon vos besoins</li>";
                $response .= "<li>Accessibilité : Facilement accessible par la clientèle</li>";
                $response .= "<li>Équipements : Prédispositions pour installations commerciales</li>";
                break;
                
            default:
                $response .= "<li>Bien entretenu et prêt à l'usage</li>";
                $response .= "<li>Emplacement stratégique</li>";
                $response .= "<li>Bon potentiel d'investissement</li>";
        }
        
        $response .= "</ul>";
        
        // Appel à l'action
        $propertyUrl = route('properties.show', ['id' => $property->id]);
        $response .= "<br><div class='mt-3'>";
        $response .= "Pour découvrir ce bien plus en détail, vous pouvez : <br>";
        $response .= "<a href='{$propertyUrl}' target='_blank' class='inline-block bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition mt-2'>Voir la fiche complète</a>";
        $response .= "</div>";
        
        $response .= "<br>Avez-vous des questions spécifiques sur cette propriété ou souhaitez-vous organiser une visite ?";
        
        return $response;
    }
    
    /**
     * Récupère l'historique de conversation
     */
    private function getConversationHistory($sessionId)
    {
        $cacheKey = 'conversation_history_' . $sessionId;
        return Cache::get($cacheKey, []);
    }
    
    /**
     * Sauvegarde l'historique de conversation
     */
    private function saveConversationHistory($sessionId, $history)
    {
        $cacheKey = 'conversation_history_' . $sessionId;
        Cache::put($cacheKey, $history, 60 * 24); // 24 heures
    }
    
    /**
     * Ajoute une réponse à l'historique de conversation
     */
    private function addResponseToHistory($sessionId, $response)
    {
        $history = $this->getConversationHistory($sessionId);
        $history[] = [
            'role' => 'assistant',
            'content' => $response
        ];
        $this->saveConversationHistory($sessionId, $history);
    }
    
    /**
     * Stocke les biens trouvés pour les questions de suivi
     */
    private function storeFoundProperties($sessionId, $properties)
    {
        $cacheKey = 'found_properties_' . $sessionId;
        $propertyIds = $properties->pluck('id')->toArray();
        Cache::put($cacheKey, $propertyIds, 60 * 24); // 24 heures
    }
    
    /**
     * Récupère les biens stockés
     */
    private function getStoredProperties($sessionId)
    {
        $cacheKey = 'found_properties_' . $sessionId;
        $propertyIds = Cache::get($cacheKey, []);
        
        if (empty($propertyIds)) {
            return [];
        }
        
        return Bien::whereIn('id', $propertyIds)->get();
    }
    
    /**
     * Extrait les informations clés du message utilisateur
     */
    private function extractInformation($message)
    {
        $lowerMessage = strtolower($message);
        $info = [
            'budget' => null,
            'type' => null,
            'transaction' => 'vente',
            'location' => null,
            'superficie' => null,
            'purpose' => null,
        ];
        
        // Extraction du budget
        if (preg_match('/budget(?:\s*[:=]?\s*)(\d+[\s\d]*)/i', $message, $matchBudget)) {
            $info['budget'] = (float) preg_replace('/\s+/', '', $matchBudget[1]);
        } elseif (preg_match('/(\d+[\s\d]*)\s*(fcfa|f|xaf)/i', $message, $matchBudget)) {
            $info['budget'] = (float) preg_replace('/\s+/', '', $matchBudget[1]);
        } elseif (preg_match('/(\d+[\s\d]*)\s*millions?/i', $message, $matchBudget)) {
            $info['budget'] = (float) preg_replace('/\s+/', '', $matchBudget[1]) * 1000000;
        }
        
        // Extraction du type de bien
        $typesMapping = [
            'maison' => 'Maison',
            'terrain' => 'Terrain',
            'local commercial' => 'LocalCommercial',
            'local' => 'LocalCommercial',
            'studio' => 'Studio',
            'chambre' => 'Chambre',
            'meublé' => 'Meublé',
            'meuble' => 'Meublé',
            'hotel' => 'Hotel',
            'hôtel' => 'Hotel',
            'appartement' => 'Maison', // Considéré comme une maison dans votre schéma
        ];
        
        foreach ($typesMapping as $keyword => $type) {
            if (strpos($lowerMessage, $keyword) !== false) {
                $info['type'] = $type;
                break;
            }
        }
        
        // Extraction du type de transaction
        if (preg_match('/(louer|location|locat)/i', $message)) {
            $info['transaction'] = 'location';
        } elseif (preg_match('/(achat|acheter|vente|achète|acheteur)/i', $message)) {
            $info['transaction'] = 'vente';
        }
        
        // Extraction de la localisation
        $locations = ['yaoundé', 'douala', 'bafoussam', 'kribi', 'limbé', 'garoua', 'maroua', 
                      'centre-ville', 'mokolo', 'nsam', 'mvan', 'dragage', 'bastos', 'mfandena'];
        
        foreach ($locations as $loc) {
            if (strpos($lowerMessage, strtolower($loc)) !== false) {
                $info['location'] = $loc;
                break;
            }
        }
        
        // Extraction de la superficie
        if (preg_match('/(\d+)\s*m(?:ètres?)?(?:\s*carrés?)?(?:\s*²)?/i', $message, $matchSuperficie)) {
            $info['superficie'] = (float) $matchSuperficie[1];
        } elseif (preg_match('/superficie(?:\s*[:=]?\s*)(\d+)/i', $message, $matchSuperficie)) {
            $info['superficie'] = (float) $matchSuperficie[1];
        }
        
        // Extraction de l'objectif/projet
        $purposes = [
            'habitation' => 'résidentiel',
            'résidence' => 'résidentiel',
            'vivre' => 'résidentiel',
            'habiter' => 'résidentiel',
            'commerce' => 'commercial',
            'commercial' => 'commercial',
            'business' => 'commercial',
            'magasin' => 'commercial',
            'boutique' => 'commercial',
            'bureau' => 'bureau',
            'investissement' => 'investissement',
            'investir' => 'investissement',
            'rendement' => 'investissement',
            'agriculture' => 'agricole',
            'agricole' => 'agricole',
            'ferme' => 'agricole',
            'cultiver' => 'agricole',
        ];
        
        foreach ($purposes as $keyword => $purpose) {
            if (strpos($lowerMessage, $keyword) !== false) {
                $info['purpose'] = $purpose;
                break;
            }
        }
        
        return $info;
    }
    
    /**
     * Détermine si le message est une question générale sur l'immobilier
     */
    private function isGeneralRealEstateQuestion($message)
    {
        $lowerMessage = strtolower($message);
        
        // Mots-clés indiquant une question générale
        $generalKeywords = [
            'comment', 'qu\'est-ce que', 'qu\'est ce que', 'quels sont', 'quelles sont',
            'pourquoi', 'expliquer', 'conseils', 'conseil', 'avis', 'recommandation',
            'meilleur moment', 'meilleure période', 'investir dans', 'investissement',
            'rentabilité', 'rendement', 'fiscalité', 'impôts', 'taxe', 'taxes',
            'prêt immobilier', 'crédit', 'hypothèque', 'notaire', 'frais', 'tendance',
            'marché immobilier', 'prix au m2', 'prix au mètre carré', 'évolution des prix',
            'que pense', 'penses-tu', 'pense tu', 'opinion', 'avis', '2025', '2024', 'futur',
            'prévision', 'tendance', 'évolution', 'zone', 'meilleure zone', 'meilleur quartier'
        ];
        
        foreach ($generalKeywords as $keyword) {
            if (strpos($lowerMessage, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Gère les questions générales sur l'immobilier
     */
    private function handleGeneralQuestion($message, $name, $sessionId, $conversationHistory = [])
    {
        // Vérifier si la réponse est en cache pour éviter de surcharger l'API
        $cacheKey = 'immo_qa_' . md5($message);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Vérifier si c'est une question de suivi sur une question générale précédente
        $isFollowUp = $this->isGeneralFollowUpQuestion($message, $conversationHistory);
        
        try {
            // Utiliser l'API LLama.cpp (ou autre modèle open source)
            $response = $this->getAIResponse($message, $name, $conversationHistory, $isFollowUp);
            
            // Mettre en cache la réponse (1 heure)
            Cache::put($cacheKey, $response, 3600);
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de réponse IA: ' . $e->getMessage());
            
            // Réponse de secours
            $fallbackResponse = $this->getFallbackResponse($message, $name, $conversationHistory);
            return $fallbackResponse;
        }
    }
    
    /**
     * Vérifie si c'est une question de suivi sur une question générale précédente
     */
    private function isGeneralFollowUpQuestion($message, $conversationHistory)
    {
        if (count($conversationHistory) < 2) {
            return false;
        }
        
        $lowerMessage = strtolower($message);
        
        // Mots-clés indiquant une question de suivi
        $followUpKeywords = [
            'et', 'aussi', 'également', 'en plus', 'par ailleurs',
            'autre', 'autres', 'plus', 'encore', 'supplémentaire',
            'pourquoi', 'comment', 'quand', 'où', 'qui', 'quoi',
            'peux-tu', 'pouvez-vous', 'peux tu', 'pouvez vous',
            'explique', 'expliques', 'expliquez', 'détaille', 'détailles', 'détaillez'
        ];
        
        foreach ($followUpKeywords as $keyword) {
            if (strpos($lowerMessage, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Obtient une réponse de l'IA pour les questions générales
     */
    private function getAIResponse($message, $name, $conversationHistory = [], $isFollowUp = false)
    {
        // Vérifier d'abord les réponses prédéfinies pour les questions courantes
        $predefinedResponse = $this->getPredefinedResponse($message, $conversationHistory);
        if ($predefinedResponse) {
            return $predefinedResponse;
        }
        
        // Option 1: Utiliser Ollama (modèle local open source)
        try {
            $response = Http::timeout(10)->post(env('OLLAMA_ENDPOINT', 'http://localhost:11434/api/generate'), [
                'model' => 'llama2',
                'prompt' => $this->buildPrompt($message, $name, $conversationHistory, $isFollowUp),
                'stream' => false
            ]);
            
            if ($response->successful()) {
                return $response->json('response');
            }
        } catch (\Exception $e) {
            Log::warning('Erreur Ollama: ' . $e->getMessage());
        }
        
        // Option 2: Utiliser l'API Hugging Face (gratuite avec limites)
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY', ''),
                    'Content-Type' => 'application/json'
                ])
                ->post('https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.2', [
                    'inputs' => $this->buildPrompt($message, $name, $conversationHistory, $isFollowUp),
                    'parameters' => [
                        'max_new_tokens' => 500,
                        'temperature' => 0.7
                    ]
                ]);
            
            if ($response->successful()) {
                return $response->json()[0]['generated_text'];
            }
        } catch (\Exception $e) {
            Log::warning('Erreur Hugging Face: ' . $e->getMessage());
        }
        
        // Si toutes les options échouent, utiliser la réponse de secours
        return $this->getFallbackResponse($message, $name, $conversationHistory);
    }
    
    /**
     * Vérifie s'il existe une réponse prédéfinie pour la question
     */
    private function getPredefinedResponse($message, $conversationHistory)
    {
        $lowerMessage = strtolower($message);
        
        // Réponses prédéfinies pour les questions courantes
        $predefinedResponses = [
            // Questions sur l'investissement
            'investissement' => "L'investissement immobilier au Cameroun peut être très rentable avec des rendements locatifs entre 8% et 12% brut annuel. Je vous conseille de privilégier les grandes villes comme Yaoundé et Douala, ou les zones touristiques comme Kribi. Assurez-vous toujours d'obtenir un titre foncier en bonne et due forme pour sécuriser votre investissement.",
            
            // Questions sur les prix
            'prix' => "Les prix immobiliers au Cameroun varient considérablement selon la localisation. À Yaoundé et Douala, comptez entre 15 000 et 150 000 FCFA/m² pour un terrain selon le quartier. Les appartements se vendent entre 250 000 et 600 000 FCFA/m². Les quartiers comme Bastos (Yaoundé) ou Bonanjo (Douala) sont les plus chers du marché.",
            
            // Questions sur les titres fonciers
            'titre foncier' => "L'obtention d'un titre foncier au Cameroun implique plusieurs étapes: demande auprès du ministère des Domaines, bornage par un géomètre assermenté, publication au Journal Officiel, et enregistrement final. Le processus peut prendre 6 mois à 2 ans et coûter entre 7% et 15% de la valeur du terrain. C'est néanmoins indispensable pour sécuriser votre propriété.",
            
            // Questions sur la location
            'location' => "Pour la location immobilière au Cameroun, prévoyez généralement 2-3 mois de caution et un mois de loyer d'avance. Un bail écrit est fortement recommandé même si les arrangements informels sont courants. Les charges (eau, électricité) sont généralement à la charge du locataire. Le rendement locatif moyen est de 8-12% brut annuel.",
            
            // Questions sur la construction
            'construction' => "Pour construire au Cameroun, vous aurez besoin d'un permis de construire délivré par la mairie (comptez 1-3 mois). Le coût de construction varie entre 150 000 et 350 000 FCFA/m² selon la qualité des finitions. Je vous recommande de travailler avec un architecte agréé et d'établir des contrats clairs avec les entrepreneurs.",
            
            // Questions sur l'immobilier en 2025
            '2025' => "Pour l'immobilier au Cameroun en 2025, les experts prévoient une croissance soutenue du marché, particulièrement dans les grandes villes comme Yaoundé et Douala. Les zones en développement comme Kribi (avec son port en pleine expansion) et Limbé (pour le tourisme) offrent d'excellentes opportunités d'investissement. Pour un hôtel, je recommanderais d'explorer les quartiers touristiques de Kribi, les zones d'affaires de Douala (Akwa, Bonanjo) ou les quartiers résidentiels haut de gamme de Yaoundé comme Bastos ou Golf. Ces zones attirent une clientèle à fort pouvoir d'achat et des expatriés, idéals pour un établissement hôtelier.",
            
            // Questions sur les hôtels
            'hotel' => "Pour construire un hôtel au Cameroun, les meilleures zones sont Kribi (tourisme balnéaire en plein essor), Limbé (tourisme), le centre de Douala (quartiers Akwa et Bonanjo pour la clientèle d'affaires), et Yaoundé (quartiers Bastos, Golf ou Centre-ville). Assurez-vous d'obtenir un terrain avec titre foncier et toutes les autorisations nécessaires. Le coût de construction d'un hôtel varie entre 400 000 et 800 000 FCFA/m² selon le standing. Le retour sur investissement moyen est de 5 à 8 ans pour un établissement bien géré.",
            
            // Questions sur les zones d'investissement
            'zone' => "Les zones les plus prometteuses pour l'investissement immobilier au Cameroun actuellement sont: Kribi (développement portuaire), Douala (quartiers Bonapriso, Bonanjo et Akwa), Yaoundé (Bastos, Golf, Dragage), et Limbé (tourisme). Pour un investissement locatif, privilégiez les quartiers proches des centres d'affaires ou des universités. Pour la construction d'un hôtel spécifiquement, Kribi offre un excellent potentiel touristique, tandis que les centres de Douala et Yaoundé sont idéaux pour la clientèle d'affaires.",
            
            // Questions sur Bamenda
            'bamenda' => "Concernant Bamenda, la situation sécuritaire actuelle dans la région du Nord-Ouest rend l'investissement immobilier plus risqué à court terme. Cependant, la ville possède un potentiel économique important à long terme grâce à sa position stratégique et son climat favorable. Si vous envisagez d'y investir, je vous conseille de privilégier les zones urbaines centrales et de vous assurer d'avoir tous les documents légaux en règle. Les prix y sont actuellement plus bas que dans les grandes métropoles comme Yaoundé et Douala, ce qui pourrait représenter une opportunité pour un investissement à long terme, mais avec un niveau de risque plus élevé.",
            
            // Autres zones à fort potentiel
            'autres zones' => "En plus des zones principales comme Kribi, Douala et Yaoundé, d'autres régions à fort potentiel d'investissement immobilier au Cameroun incluent:<br><br>1. <strong>Buea</strong>: Ville universitaire avec une forte demande locative de la part des étudiants.<br>2. <strong>Bafoussam</strong>: Capitale de l'Ouest en plein développement économique.<br>3. <strong>Edéa</strong>: Zone industrielle en croissance grâce à sa proximité avec Douala.<br>4. <strong>Garoua</strong>: Principale ville du Nord avec un potentiel de développement touristique et commercial.<br>5. <strong>Ngaoundéré</strong>: Point stratégique pour le commerce avec les pays voisins.<br><br>Ces zones offrent généralement un meilleur rapport qualité-prix que les grandes métropoles, avec des perspectives de plus-value intéressantes à moyen terme."
        ];
        
        // Vérifier si le message contient des mots-clés pour les réponses prédéfinies
        foreach ($predefinedResponses as $keyword => $response) {
            if (strpos($lowerMessage, $keyword) !== false) {
                // Si c'est une question de suivi sur Bamenda
                if ($keyword === 'bamenda' && $this->isFollowUpQuestionAboutZones($message, $conversationHistory)) {
                    return $predefinedResponses['bamenda'];
                }
                
                // Si c'est une question de suivi sur d'autres zones
                if (($keyword === 'zone' || $keyword === '2025') && 
                    (strpos($lowerMessage, 'autres') !== false || 
                     strpos($lowerMessage, 'autre') !== false || 
                     strpos($lowerMessage, 'plus') !== false || 
                     strpos($lowerMessage, 'encore') !== false)) {
                    return $predefinedResponses['autres zones'];
                }
                
                return $response;
            }
        }
        
        return null;
    }
    
    /**
     * Vérifie si c'est une question de suivi sur les zones d'investissement
     */
    private function isFollowUpQuestionAboutZones($message, $conversationHistory)
    {
        if (count($conversationHistory) < 2) {
            return false;
        }
        
        $lowerMessage = strtolower($message);
        
        // Vérifier si le message mentionne Bamenda
        if (strpos($lowerMessage, 'bamenda') !== false) {
            
            // Vérifier si une question précédente concernait les zones d'investissement
            foreach (array_reverse($conversationHistory) as $entry) {
                if ($entry['role'] === 'assistant' && 
                    (strpos(strtolower($entry['content']), 'zone') !== false || 
                     strpos(strtolower($entry['content']), 'investissement') !== false || 
                     strpos(strtolower($entry['content']), 'kribi') !== false || 
                     strpos(strtolower($entry['content']), 'douala') !== false || 
                     strpos(strtolower($entry['content']), 'yaoundé') !== false)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Construit le prompt pour l'IA
     */
    private function buildPrompt($message, $name, $conversationHistory = [], $isFollowUp = false)
    {
        $prompt = "Tu es Immo, un assistant virtuel spécialisé dans l'immobilier au Cameroun. Tu dois répondre de manière professionnelle, précise et utile à toutes les questions immobilières.\n\n";
        
        // Ajouter le contexte de la conversation si c'est une question de suivi
        if ($isFollowUp && !empty($conversationHistory)) {
            $prompt .= "Voici l'historique récent de la conversation:\n\n";
            
            // Limiter à 3 échanges maximum pour éviter un prompt trop long
            $recentHistory = array_slice($conversationHistory, -6);
            foreach ($recentHistory as $entry) {
                $role = $entry['role'] === 'user' ? $name : 'Immo';
                $prompt .= $role . ": " . $entry['content'] . "\n\n";
            }
        }
        
        $prompt .= "Contexte: Tu parles à $name qui te pose la question suivante: \"$message\"\n\n";
        
        $prompt .= "Réponds uniquement aux questions liées à l'immobilier. Si la question n'est pas liée à l'immobilier, indique poliment que tu es spécialisé dans l'immobilier et propose de l'aide sur ce sujet.\n\n";
        
        $prompt .= "Utilise ces informations sur le marché immobilier camerounais:
- Les prix au m² varient considérablement selon les villes et quartiers
- À Yaoundé et Douala, les prix des terrains oscillent entre 15 000 et 150 000 FCFA/m² selon l'emplacement
- Les quartiers haut de gamme comme Bastos à Yaoundé ou Bonanjo à Douala sont les plus chers
- Le rendement locatif moyen est de 8-12% brut annuel
- Les frais de notaire représentent environ 7-9% du prix d'achat
- Le marché est principalement informel avec peu d'agences immobilières professionnelles
- L'obtention d'un titre foncier est cruciale mais parfois complexe\n\n";
        
        $prompt .= "Réponds de manière concise, informative et professionnelle.";
        
        return $prompt;
    }
    
    /**
     * Fournit une réponse de secours basée sur des modèles prédéfinis
     */
    private function getFallbackResponse($message, $name, $conversationHistory = [])
    {
        $lowerMessage = strtolower($message);
        
        // Réponses prédéfinies pour les questions courantes
        $commonResponses = [
            'investissement' => "L'investissement immobilier au Cameroun peut être très rentable avec des rendements locatifs entre 8% et 12% brut annuel. Je vous conseille de privilégier les grandes villes comme Yaoundé et Douala, ou les zones touristiques comme Kribi. Assurez-vous toujours d'obtenir un titre foncier en bonne et due forme pour sécuriser votre investissement.",
            
            'prix' => "Les prix immobiliers au Cameroun varient considérablement selon la localisation. À Yaoundé et Douala, comptez entre 15 000 et 150 000 FCFA/m² pour un terrain selon le quartier. Les appartements se vendent entre 250 000 et 600 000 FCFA/m². Les quartiers comme Bastos (Yaoundé) ou Bonanjo (Douala) sont les plus chers du marché.",
            
            'titre foncier' => "L'obtention d'un titre foncier au Cameroun implique plusieurs étapes: demande auprès du ministère des Domaines, bornage par un géomètre assermenté, publication au Journal Officiel, et enregistrement final. Le processus peut prendre 6 mois à 2 ans et coûter entre 7% et 15% de la valeur du terrain. C'est néanmoins indispensable pour sécuriser votre propriété.",
            
            'location' => "Pour la location immobilière au Cameroun, prévoyez généralement 2-3 mois de caution et un mois de loyer d'avance. Un bail écrit est fortement recommandé même si les arrangements informels sont courants. Les charges (eau, électricité) sont généralement à la charge du locataire. Le rendement locatif moyen est de 8-12% brut annuel.",
            
            'construction' => "Pour construire au Cameroun, vous aurez besoin d'un permis de construire délivré par la mairie (comptez 1-3 mois). Le coût de construction varie entre 150 000 et 350 000 FCFA/m² selon la qualité des finitions. Je vous recommande de travailler avec un architecte agréé et d'établir des contrats clairs avec les entrepreneurs."
        ];
        
        // Rechercher des mots-clés dans le message
        foreach ($commonResponses as $keyword => $response) {
            if (strpos($lowerMessage, $keyword) !== false) {
                return $response;
            }
        }
        
        // Vérifier si c'est une question de suivi
        if (!empty($conversationHistory) && count($conversationHistory) >= 2) {
            $lastAssistantMessage = null;
            
            // Trouver le dernier message de l'assistant
            foreach (array_reverse($conversationHistory) as $entry) {
                if ($entry['role'] === 'assistant') {
                    $lastAssistantMessage = $entry['content'];
                    break;
                }
            }
            
            if ($lastAssistantMessage) {
                return "Pour répondre à votre question sur " . $this->extractTopicFromMessage($message) . ", je vous recommande de considérer les aspects suivants du marché immobilier camerounais : la localisation est primordiale, assurez-vous d'obtenir tous les documents légaux nécessaires, et consultez des professionnels locaux pour une évaluation précise. Avez-vous une question plus spécifique sur ce sujet ?";
            }
        }
        
        // Réponse par défaut si aucun mot-clé n'est trouvé
        return "Merci pour votre question sur l'immobilier. Pour vous donner une réponse plus précise, pourriez-vous me donner plus de détails sur ce que vous recherchez exactement ? Par exemple, êtes-vous intéressé par l'achat, la location, l'investissement ou la construction ?";
    }
    
    /**
     * Extrait le sujet principal d'un message
     */
    private function extractTopicFromMessage($message)
    {
        $lowerMessage = strtolower($message);
        
        $topics = [
            'investissement' => 'l\'investissement immobilier',
            'prix' => 'les prix immobiliers',
            'achat' => 'l\'achat immobilier',
            'vente' => 'la vente immobilière',
            'location' => 'la location immobilière',
            'construction' => 'la construction',
            'terrain' => 'les terrains',
            'maison' => 'les maisons',
            'appartement' => 'les appartements',
            'titre foncier' => 'les titres fonciers',
            'notaire' => 'les aspects juridiques',
            'rendement' => 'le rendement immobilier',
            'zone' => 'les zones d\'investissement',
            'quartier' => 'les quartiers',
            'hotel' => 'l\'investissement hôtelier'
        ];
        
        foreach ($topics as $keyword => $topic) {
            if (strpos($lowerMessage, $keyword) !== false) {
                return $topic;
            }
        }
        
        return 'l\'immobilier';
    }
    
    /**
     * Recherche des biens correspondant aux critères extraits
     */
    private function findMatchingProperties($criteria)
    {
        try {
            // Commencer avec une requête de base
            $query = Bien::where('statut', 'Disponible');
            
            // Appliquer le filtre de transaction seulement si spécifié
            if ($criteria['transaction']) {
                $query->where('transaction_type', $criteria['transaction']);
            }
            
            // Appliquer le filtre de type seulement si spécifié
            if ($criteria['type']) {
                $query->where('type', $criteria['type']);
            }
            
            // Appliquer le filtre de budget avec une marge plus large
            if ($criteria['budget']) {
                // Marge de ±30% sur le budget pour trouver plus de résultats
                $minPrice = $criteria['budget'] * 0.7;
                $maxPrice = $criteria['budget'] * 1.3;
                $query->whereBetween('prix', [$minPrice, $maxPrice]);
            }
            
            // Appliquer le filtre de localisation
            if ($criteria['location']) {
                $query->where(function($q) use ($criteria) {
                    $q->where('adresse', 'like', '%' . $criteria['location'] . '%')
                      ->orWhere('description', 'like', '%' . $criteria['location'] . '%')
                      ->orWhere('titre', 'like', '%' . $criteria['location'] . '%');
                });
            }
            
            // Appliquer le filtre de superficie
            if ($criteria['superficie']) {
                // Marge de ±20% sur la superficie
                $minArea = $criteria['superficie'] * 0.8;
                $maxArea = $criteria['superficie'] * 1.2;
                $query->whereBetween('superficie', [$minArea, $maxArea]);
            }
            
            // Tri par pertinence
            $query->orderBy('prix', 'asc');
            
            // Récupérer les résultats
            $results = $query->take(5)->get();
            
            // Si aucun résultat, essayer une recherche plus souple
            if ($results->isEmpty() && ($criteria['type'] || $criteria['budget'] || $criteria['location'])) {
                // Nouvelle requête plus souple
                $looseQuery = Bien::where('statut', 'Disponible');
                
                // Garder seulement un critère pour élargir la recherche
                if ($criteria['type']) {
                    $looseQuery->where('type', $criteria['type']);
                } elseif ($criteria['location']) {
                    $looseQuery->where(function($q) use ($criteria) {
                        $q->where('adresse', 'like', '%' . $criteria['location'] . '%')
                          ->orWhere('description', 'like', '%' . $criteria['location'] . '%')
                          ->orWhere('titre', 'like', '%' . $criteria['location'] . '%');
                    });
                } elseif ($criteria['budget']) {
                    // Marge de ±50% sur le budget
                    $minPrice = $criteria['budget'] * 0.5;
                    $maxPrice = $criteria['budget'] * 1.5;
                    $looseQuery->whereBetween('prix', [$minPrice, $maxPrice]);
                }
                
                $results = $looseQuery->take(5)->get();
            }
            
            return $results;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche de biens: ' . $e->getMessage());
            return collect(); // Retourner une collection vide en cas d'erreur
        }
    }
    
    /**
     * Construit la réponse finale avec les biens trouvés
     */
    private function buildResponse($properties, $criteria, $name, $sessionId)
    {
        // Si aucun bien n'est trouvé
        if ($properties->isEmpty()) {
            // Enregistrer la recherche infructueuse pour amélioration future
            $this->logSearchQuery($criteria, $sessionId, 0);
            
            // Suggérer d'élargir les critères
            $suggestions = $this->suggestAlternatives($criteria);
            
            return "Bonjour, je n'ai pas trouvé de biens correspondant exactement à vos critères. " . $suggestions;
        }
        
        // Enregistrer la recherche réussie
        $this->logSearchQuery($criteria, $sessionId, count($properties));
        
        // Construction de la réponse avec les biens trouvés
        $reply = "Bonjour, j'ai trouvé " . count($properties) . " bien" . (count($properties) > 1 ? "s" : "") . " qui pourrai" . (count($properties) > 1 ? "ent" : "t") . " vous intéresser :<br><br>";
        
        foreach ($properties as $index => $bien) {
            $propertyUrl = route('properties.show', ['id' => $bien->id]);
            
            // Ajouter un badge "Meilleure offre" pour le premier bien
            $badgeHtml = ($index === 0) ? '<span class="inline-block bg-green-100 text-green-800 px-2 py-1 text-xs font-semibold rounded">Meilleure offre</span> ' : '';
            
            $reply .= "<div class='mb-4 p-3 border border-gray-200 rounded-lg'>";
            $reply .= "{$badgeHtml}<strong class='text-lg'>{$bien->titre}</strong> ({$bien->type})<br>";
            $reply .= "<span class='text-gray-700'>Prix : " . number_format($bien->prix, 0, ',', ' ') . " FCFA | Superficie : {$bien->superficie} m²</span><br>";
            
            // Ajouter des détails pertinents de la description
            $shortDesc = substr($bien->description, 0, 120) . (strlen($bien->description) > 120 ? '...' : '');
            $reply .= "<p class='my-2'>{$shortDesc}</p>";
            
            // Ajouter des avantages spécifiques selon le type de bien
            $reply .= $this->getPropertyAdvantages($bien);
            
            $reply .= "<div class='mt-2'><a href='{$propertyUrl}' target='_blank' class='inline-block bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition'>Voir ce bien</a></div>";
            $reply .= "</div>";
        }
        
        // Ajouter une question de suivi pour maintenir la conversation
        $reply .= $this->getFollowUpQuestion($criteria);
        
        return $reply;
    }
    
    /**
     * Génère des avantages spécifiques selon le type de bien
     */
    private function getPropertyAdvantages($bien)
    {
        $advantages = "<ul class='list-disc pl-5 my-2 text-sm'>";
        
        switch ($bien->type) {
            case 'Maison':
                $advantages .= "<li>Excellent emplacement dans le quartier</li>";
                $advantages .= "<li>Sécurité 24h/24</li>";
                break;
                
            case 'Terrain':
                $advantages .= "<li>Terrain plat, facile à construire</li>";
                $advantages .= "<li>Titre foncier disponible</li>";
                break;
                
            case 'LocalCommercial':
                $advantages .= "<li>Fort passage commercial</li>";
                $advantages .= "<li>Visibilité exceptionnelle</li>";
                break;
                
            case 'Studio':
            case 'Chambre':
            case 'Meublé':
                $advantages .= "<li>Proche des commodités</li>";
                $advantages .= "<li>Quartier calme et sécurisé</li>";
                break;
                
            case 'Hotel':
                $advantages .= "<li>Potentiel de rendement élevé</li>";
                $advantages .= "<li>Clientèle existante</li>";
                break;
        }
        
        $advantages .= "</ul>";
        return $advantages;
    }
    
    /**
     * Génère une question de suivi pour maintenir la conversation
     */
    private function getFollowUpQuestion($criteria)
    {
        $questions = [
            "Ces biens correspondent-ils à vos attentes ? Je peux affiner la recherche selon vos préférences.",
            "Souhaitez-vous des informations supplémentaires sur l'un de ces biens ?",
            "Avez-vous une préférence particulière concernant le quartier ?",
            "Quel est votre calendrier pour ce projet immobilier ?",
            "Préférez-vous organiser une visite physique ou virtuelle pour l'un de ces biens ?"
        ];
        
        return "<p class='mt-3'>" . $questions[array_rand($questions)] . "</p>";
    }
    
    /**
     * Suggère des alternatives quand aucun bien ne correspond
     */
    private function suggestAlternatives($criteria)
    {
        $suggestions = "Voici quelques suggestions pour élargir votre recherche :<br><ul class='list-disc pl-5 my-2'>";
        
        if ($criteria['budget']) {
            $suggestions .= "<li>Augmenter votre budget d'environ 20% pourrait vous donner accès à plus d'options</li>";
        }
        
        if ($criteria['type']) {
            $suggestions .= "<li>Envisager d'autres types de biens similaires pourrait être intéressant</li>";
        }
        
        if ($criteria['location']) {
            $suggestions .= "<li>Explorer les quartiers voisins qui offrent souvent un meilleur rapport qualité-prix</li>";
        }
        
        $suggestions .= "</ul><br>Souhaitez-vous que je vous propose des alternatives spécifiques ?";
        
        return $suggestions;
    }
    
    /**
     * Enregistre les requêtes de recherche pour amélioration future
     */
    private function logSearchQuery($criteria, $sessionId, $resultCount)
    {
        // Cette fonction pourrait enregistrer les recherches dans une table dédiée
        // pour analyser les tendances et améliorer les réponses futures
        
        // Exemple simplifié avec Log
        Log::info('Recherche immobilière', [
            'session_id' => $sessionId,
            'criteria' => $criteria,
            'result_count' => $resultCount,
            'timestamp' => now()
        ]);
        
        // Idéalement, vous créeriez une table SearchLog pour stocker ces informations
    }

    /**
     * Vérifie si le message est une salutation ou une question sur l'identité du bot
     */
    private function isGreetingOrIdentityQuestion($message)
    {
        $lowerMessage = strtolower($message);
        $patterns = [
            '/^(salut|bonjour|bonsoir|hello|hi|hey|coucou)/',
            '/(que fais[ -]tu|que faites[ -]vous|à quoi sers[ -]tu|que peux[ -]tu faire|peux[ -]tu m\'aider)/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $lowerMessage)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Vérifie si le message est une demande de bien immobilier
     */
    private function isPropertyRequest($message)
    {
        $lowerMessage = strtolower($message);
        $patterns = [
            '/(cherche|recherche|veux|aimerais|souhaite)\s+(un|une|des)\s+(maison|terrain|appartement|studio|local|chambre|meublé|hotel)/',
            '/(acheter|louer|acquérir)\s+(un|une|des)\s+(maison|terrain|appartement|studio|local|chambre|meublé|hotel)/',
            '/budget\s+de\s+\d+/',
            '/\d+\s+(fcfa|f|xaf)/',
            '/(maison|terrain|appartement|studio|local|chambre|meublé|hotel)\s+(à|a)\s+(vendre|louer)/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $lowerMessage)) {
                return true;
            }
        }
        
        return false;
    }
}
