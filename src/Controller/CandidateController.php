<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Repository\CandidateRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Provider\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\UuidV7;

#[Route('/v1/candidates')]
class CandidateController extends AbstractController
{
    #[Route('/', name: 'app_candidate_list', methods: ['GET'])]
    public function allCandidates(CandidateRepository $candidateRepository, SerializerInterface $serializer): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => "Récupération de la liste des centres d'ABC Formation",
            'centers' => json_decode($serializer->serialize($candidateRepository->findAll(), 'json'), true),
        ]);
    }

    #[Route('/{email}', name: 'app_candidate_show', methods: ['GET'])]
    public function aCandidate(CandidateRepository $candidateRepository, SerializerInterface $serializer, string $email): JsonResponse
    {
        $candidate = $candidateRepository->findOneBy(['email_candidate' => $email]);
        if ($candidate instanceof Candidate) {
            return $this->json([
                'success' => true,
                'message' => 'Affichage du candidat `' . $candidate->getEmailCandidate() . '` !',
                'candidate' => json_decode($serializer->serialize($candidate, 'json'), true),
            ]);
        }
        return $this->json([
            'success' => false,
            'message' => 'Le candidat recherché est inconnu !'
        ]);
    }

    #[Route('/{email}/verify/{uuid}', name: 'app_candidate_confirm', methods: ['GET'])]
    public function confirm(ManagerRegistry $doctrine, CandidateRepository $candidateRepository, SerializerInterface $serializer, string $email, string $uuid): JsonResponse
    {
        $candidate = $candidateRepository->findOneBy(['email_candidate' => $email]);
        if ($candidate instanceof Candidate && $candidate->getUuidCandidate()->toHex() === $uuid && $candidate->isIsEnabledCandidate() === false) {
            $candidate->setIsEnabledCandidate(true);

            $em = $doctrine->getManager();
            $em->persist($candidate);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Félicitation, vous avez réussi !',
                'candidate' => json_decode($serializer->serialize($candidate, 'json'), true),
            ]);
        }
        return $this->json([
            'success' => false,
            'message' => 'La confirmation s\'est mal passée ! Veuillez réessayer...'
        ]);
    }

    #[Route('/', name: 'app_candidate_add', methods: ['POST'])]
    public function addCandidate(ManagerRegistry $doctrine, CandidateRepository $candidateRepository, Request $request, MailerInterface $mailer): JsonResponse
    {
        if (strlen($request->request->get("candidate_email")) >= 7 && strlen($request->request->get("candidate_email")) <= 255) {
            if (filter_var($request->request->get("candidate_email"), FILTER_VALIDATE_EMAIL)) {
                if (!$candidateRepository->findOneBy(['email_candidate' => $request->request->get("candidate_email")]) instanceof Candidate) {
                    $em = $doctrine->getManager();
                    $candidate = new Candidate();
                    $candidate->setEmailCandidate($request->request->get("candidate_email"));
                    if (!empty($request->request->get("candidate_dob"))) {
                        $candidate->setDobCandidate(date_create_immutable($request->request->get("candidate_dob")));
                    }
                    $em->persist($candidate);
                    $em->flush();

                    // Envoie de l'email
                    $email = (new Email())
                        ->from('contact@marceau-rodrigues.fr')
                        ->to($candidate->getEmailCandidate())
                        //->cc('cc@example.com')
                        ->bcc('marceau0707@gmail.com')
                        ->replyTo('contact@marceau-rodrigues.fr')
                        ->priority(Email::PRIORITY_HIGH)
                        ->subject('[Rallye des Métiers] Confirmez votre email pour finaliser vos missions')
                        ->text('Sending emails is fun again!')
                        ->html('<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                        <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
                        <head>
                        <!--[if gte mso 9]>
                        <xml>
                          <o:OfficeDocumentSettings>
                            <o:AllowPNG/>
                            <o:PixelsPerInch>96</o:PixelsPerInch>
                          </o:OfficeDocumentSettings>
                        </xml>
                        <![endif]-->
                          <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                          <meta name="viewport" content="width=device-width, initial-scale=1.0">
                          <meta name="x-apple-disable-message-reformatting">
                          <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
                          <title></title>
                          
                            <style type="text/css">
                              @media only screen and (min-width: 620px) {
                          .u-row {
                            width: 600px !important;
                          }
                          .u-row .u-col {
                            vertical-align: top;
                          }
                        
                          .u-row .u-col-40p5 {
                            width: 243px !important;
                          }
                        
                          .u-row .u-col-59p5 {
                            width: 357px !important;
                          }
                        
                          .u-row .u-col-100 {
                            width: 600px !important;
                          }
                        
                        }
                        
                        @media (max-width: 620px) {
                          .u-row-container {
                            max-width: 100% !important;
                            padding-left: 0px !important;
                            padding-right: 0px !important;
                          }
                          .u-row .u-col {
                            min-width: 320px !important;
                            max-width: 100% !important;
                            display: block !important;
                          }
                          .u-row {
                            width: 100% !important;
                          }
                          .u-col {
                            width: 100% !important;
                          }
                          .u-col > div {
                            margin: 0 auto;
                          }
                        }
                        body {
                          margin: 0;
                          padding: 0;
                        }
                        
                        table,
                        tr,
                        td {
                          vertical-align: top;
                          border-collapse: collapse;
                        }
                        
                        p {
                          margin: 0;
                        }
                        
                        .ie-container table,
                        .mso-container table {
                          table-layout: fixed;
                        }
                        
                        * {
                          line-height: inherit;
                        }
                        
                        a[x-apple-data-detectors=\'true\'] {
                          color: inherit !important;
                          text-decoration: none !important;
                        }
                        
                        @media (max-width: 480px) {
                          .hide-mobile {
                            max-height: 0px;
                            overflow: hidden;
                            display: none !important;
                          }
                        }
                        
                        table, td { color: #000000; } #u_body a { color: #cca250; text-decoration: none; } @media (max-width: 480px) { #u_content_heading_3 .v-container-padding-padding { padding: 10px 20px !important; } #u_content_heading_3 .v-font-size { font-size: 28px !important; } #u_content_text_3 .v-container-padding-padding { padding: 10px 22px 26px !important; } }
                            </style>
                          
                          
                        
                        <!--[if !mso]><!--><link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet" type="text/css"><!--<![endif]-->
                        
                        </head>
                        
                        <body class="clean-body u_body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #f9f9f9;color: #000000">
                          <!--[if IE]><div class="ie-container"><![endif]-->
                          <!--[if mso]><div class="mso-container"><![endif]-->
                          <table id="u_body" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #f9f9f9;width:100%" cellpadding="0" cellspacing="0">
                          <tbody>
                          <tr style="vertical-align: top">
                            <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                            <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color: #f9f9f9;"><![endif]-->
                            
                          
                          
                        <div class="u-row-container" style="padding: 0px;background-color: transparent">
                          <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                              <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: transparent;"><![endif]-->
                              
                        <!--[if (mso)|(IE)]><td align="center" width="243" style="background-color: #111114;width: 243px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;" valign="top"><![endif]-->
                        <div class="u-col u-col-40p5" style="max-width: 320px;min-width: 243px;display: table-cell;vertical-align: top;">
                          <div style="background-color: #111114;height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                          <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"><!--<![endif]-->
                          
                        <table style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                          <tbody>
                            <tr>
                              <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:\'Montserrat\',sans-serif;" align="left">
                                
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                          <tr>
                            <td style="padding-right: 0px;padding-left: 0px;" align="center">
                              
                              <img align="center" border="0" src="https://docs.marceau-rodrigues.fr/assets/images/logo_2.png" alt="Logo" title="Logo" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 100%;max-width: 223px;" width="223"/>
                              
                            </td>
                          </tr>
                        </table>
                        
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                          <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                          </div>
                        </div>
                        <!--[if (mso)|(IE)]></td><![endif]-->
                        <!--[if (mso)|(IE)]><td align="center" width="357" style="background-color: #111114;width: 357px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;" valign="top"><![endif]-->
                        <div class="u-col u-col-59p5" style="max-width: 320px;min-width: 357px;display: table-cell;vertical-align: top;">
                          <div style="background-color: #111114;height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                          <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"><!--<![endif]-->
                          
                        <table class="hide-mobile" style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                          <tbody>
                            <tr>
                              <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:\'Montserrat\',sans-serif;" align="left">
                                
                          <div class="v-font-size" style="font-size: 20px; color: #ffffff; line-height: 250%; text-align: left; word-wrap: break-word;">
                            <p style="line-height: 250%;"><strong>Forma</strong>Docs</p>
                          </div>
                        
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                          <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                          </div>
                        </div>
                        <!--[if (mso)|(IE)]></td><![endif]-->
                              <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                          </div>
                          </div>
                          
                        
                        
                          
                          
                        <div class="u-row-container" style="padding: 0px;background-color: transparent">
                          <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                              <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: transparent;"><![endif]-->
                              
                        <!--[if (mso)|(IE)]><td align="center" width="600" style="background-color: #fffefe;width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;" valign="top"><![endif]-->
                        <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                          <div style="background-color: #fffefe;height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                          <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"><!--<![endif]-->
                          
                        <table id="u_content_heading_3" style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                          <tbody>
                            <tr>
                              <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px 55px;font-family:\'Montserrat\',sans-serif;" align="left">
                                
                          <!--[if mso]><table width="100%"><tr><td><![endif]-->
                            <h1 class="v-font-size" style="margin: 0px; line-height: 160%; text-align: center; word-wrap: break-word; font-family: \'Montserrat\',sans-serif; font-size: 33px; font-weight: 400;"><span><span><strong>Il ne vous reste plus qu\'une étape pour compléter l\'épreuve !</strong></span></span></h1>
                          <!--[if mso]></td></tr></table><![endif]-->
                        
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                        <table id="u_content_text_3" style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                          <tbody>
                            <tr>
                              <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px 60px 50px;font-family:\'Montserrat\',sans-serif;" align="left">
                                
                          <div class="v-font-size" style="font-size: 14px; color: #444444; line-height: 170%; text-align: center; word-wrap: break-word;">
                            <p style="font-size: 14px; line-height: 170%;"><span style="font-size: 16px; line-height: 27.2px;">Félicitations ! Vous êtes parvenu·e à vous inscrire en base de données. </span></p>
                        <p style="font-size: 14px; line-height: 170%;"><span style="font-size: 16px; line-height: 27.2px;">Il ne vous reste plus qu\'à cliquer sur le lien ci-après pour parvenir à terminer l\'épreuve du Pôle Numérique.</span></p>
                          </div>
                        
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                          <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                          </div>
                        </div>
                        <!--[if (mso)|(IE)]></td><![endif]-->
                              <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                          </div>
                          </div>
                          
                        
                        
                          
                          
                        <div class="u-row-container" style="padding: 0px;background-color: transparent">
                          <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
                            <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                              <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #ffffff;"><![endif]-->
                              
                        <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;" valign="top"><![endif]-->
                        <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                          <div style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                          <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"><!--<![endif]-->
                          
                        <table style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                          <tbody>
                            <tr>
                              <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px 10px 50px;font-family:\'Montserrat\',sans-serif;" align="left">
                                
                          <!--[if mso]><style>.v-button {background: transparent !important;}</style><![endif]-->
                        <div align="center">
                          <!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="https://api.marceau-rodrigues.fr/v1/candidates/' . $candidate->getEmailCandidate() . '/verify/' . $candidate->getUuidCandidate()->toHex() . '" style="height:47px; v-text-anchor:middle; width:263px;" arcsize="8.5%"  stroke="f" fillcolor="#65c0ed"><w:anchorlock/><center style="color:#FFFFFF;"><![endif]-->
                            <a href="https://api.marceau-rodrigues.fr/v1/candidates/' . $candidate->getEmailCandidate() . '/verify/' . $candidate->getUuidCandidate()->toHex() . '" target="_blank" class="v-button v-font-size" style="box-sizing: border-box;display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #FFFFFF; background-color: #65c0ed; border-radius: 4px;-webkit-border-radius: 4px; -moz-border-radius: 4px; width:auto; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;font-size: 14px;">
                              <span style="display:block;padding:14px 33px;line-height:120%;"><strong><span style="font-size: 16px; line-height: 19.2px;">Confirmer mon compte</span></strong></span>
                            </a>
                            <!--[if mso]></center></v:roundrect><![endif]-->
                        </div>
                        
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                          <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                          </div>
                        </div>
                        <!--[if (mso)|(IE)]></td><![endif]-->
                              <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                          </div>
                          </div>
                          
                        
                        
                          
                          
                        <div class="u-row-container" style="padding: 0px;background-color: transparent">
                          <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #111114;">
                            <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                              <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #111114;"><![endif]-->
                              
                        <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;" valign="top"><![endif]-->
                        <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                          <div style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                          <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"><!--<![endif]-->
                          
                        <table style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                          <tbody>
                            <tr>
                              <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:32px 10px 0px;font-family:\'Montserrat\',sans-serif;" align="left">
                                
                          <div class="v-font-size" style="font-size: 14px; color: #ffffff; line-height: 140%; text-align: center; word-wrap: break-word;">
                            <p style="font-size: 14px; line-height: 140%;"><span style="font-size: 18px; line-height: 25.2px;"><strong>Forma</strong>Docs</span></p>
                          </div>
                        
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                        <table style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                          <tbody>
                            <tr>
                              <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:\'Montserrat\',sans-serif;" align="left">
                                
                          <div class="v-font-size" style="font-size: 14px; color: #b0b1b4; line-height: 180%; text-align: center; word-wrap: break-word;">
                            <p style="font-size: 14px; line-height: 180%;">4657 rue de la Jeune Parque, 34070 - Montpellier</p>
                          </div>
                        
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                        <table style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                          <tbody>
                            <tr>
                              <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:\'Montserrat\',sans-serif;" align="left">
                                
                        <div align="center">
                          <div style="display: table; max-width:105px;">
                          <!--[if (mso)|(IE)]><table width="105" cellpadding="0" cellspacing="0" border="0"><tr><td style="border-collapse:collapse;" align="center"><table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace: 0pt;mso-table-rspace: 0pt; width:105px;"><tr><![endif]-->
                          
                            
                            <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 21px;" valign="top"><![endif]-->
                            <table align="left" border="0" cellspacing="0" cellpadding="0" width="32" height="32" style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 21px">
                              <tbody><tr style="vertical-align: top"><td align="left" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                <a href="https://github.com/MarceauAdrar?tab=repositories" title="GitHub" target="_blank">
                                  <img src="https://docs.marceau-rodrigues.fr/assets/images/github.png" alt="GitHub" title="GitHub" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                </a>
                              </td></tr>
                            </tbody></table>
                            <!--[if (mso)|(IE)]></td><![endif]-->
                            
                            <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 0px;" valign="top"><![endif]-->
                            <table align="left" border="0" cellspacing="0" cellpadding="0" width="32" height="32" style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 0px">
                              <tbody><tr style="vertical-align: top"><td align="left" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                <a href="https://www.linkedin.com/school/adrarnumerique/posts/?feedView=all" title="LinkedIn" target="_blank">
                                  <img src="https://docs.marceau-rodrigues.fr/assets/images/linkedin.png" alt="LinkedIn" title="LinkedIn" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                </a>
                              </td></tr>
                            </tbody></table>
                            <!--[if (mso)|(IE)]></td><![endif]-->
                            
                            
                            <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                          </div>
                        </div>
                        
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                        <table style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                          <tbody>
                            <tr>
                              <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:\'Montserrat\',sans-serif;" align="left">
                                
                          <table height="0px" align="center" border="0" cellpadding="0" cellspacing="0" width="82%" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 1px solid #9495a7;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                            <tbody>
                              <tr style="vertical-align: top">
                                <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;font-size: 0px;line-height: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                  <span>&#160;</span>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                        <table style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                          <tbody>
                            <tr>
                              <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:0px 10px 13px;font-family:\'Montserrat\',sans-serif;" align="left">
                                
                          <div class="v-font-size" style="font-size: 14px; color: #b0b1b4; line-height: 180%; text-align: center; word-wrap: break-word;">
                            <p style="font-size: 14px; line-height: 180%;">© 2024 - Tous Droits Réservés</p>
                          </div>
                        
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                          <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
                          </div>
                        </div>
                        <!--[if (mso)|(IE)]></td><![endif]-->
                              <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                          </div>
                          </div>
                          
                        
                        
                            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                            </td>
                          </tr>
                          </tbody>
                          </table>
                          <!--[if mso]></div><![endif]-->
                          <!--[if IE]></div><![endif]-->
                        </body>
                        
                        </html>');
                    $mailer->send($email);

                    return $this->json([
                        'success' => true,
                        'message' => "Candidat " . $candidate->getEmailCandidate() . " ajouté.\nRendez-vous sur votre boîte mail pour la dernière étape !"
                    ]);
                }
                return $this->json([
                    'success' => false,
                    'message' => "Le candidat existe déjà.\nRendez-vous sur votre boîte mail pour la dernière étape demandée !"
                ]);
            }
            return $this->json([
                'success' => false,
                'message' => "Format de l'email incorrect..."
            ]);
        }
        return $this->json([
            'success' => false,
            'message' => "Taille de l'email insuffisante... (7 caractères minimum, 255 maximum)"
        ]);
    }

    #[Route('/{email}', name: 'app_candidate_edit', methods: ['PUT', 'PATCH'])]
    public function editCandidate(ManagerRegistry $doctrine, CandidateRepository $candidateRepository, Request $request, string $email): JsonResponse
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $em = $doctrine->getManager();
            $candidate = $candidateRepository->findOneBy(['email_candidate' => $email]);
            $data = json_decode($request->getContent(), true);
            if (isset($data) && !empty($data)) {
                if ($candidate instanceof Candidate) {
                    if (!empty($data["candidate_email"]) && strlen($data["candidate_email"]) >= 7 && strlen($data["candidate_email"]) <= 255) {
                        $candidate->setEmailCandidate($data["candidate_email"]);
                    }
                    if (!empty($data["candidate_dob"])) {
                        $candidate->setDobCandidate(date_create_immutable($data["candidate_dob"]));
                    }
                    $em->persist($candidate);
                    $em->flush();
                    return $this->json([
                        'success' => true,
                        'message' => "Candidat " . $candidate->getEmailCandidate() . " mis à jour.\nRendez-vous sur votre boîte mail pour la dernière étape !"
                    ]);
                }
                return $this->json([
                    'success' => false,
                    'message' => "Le candidat recherché n'existe pas..."
                ]);
            }
            return $this->json([
                'success' => false,
                'message' => "Avez-vous bien formaté le formulaire ? `Body`->`Raw` et les éléments au format application/json"
            ]);
        }
        return $this->json([
            'success' => false,
            'message' => "Format de l'email incorrect..."
        ]);
    }

    #[Route('/{email}', name: 'app_candidate_delete', methods: ['DELETE'])]
    public function deleteCandidate(ManagerRegistry $doctrine, CandidateRepository $candidateRepository, Request $request, string $email): JsonResponse
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) >= 7 && strlen($email) <= 255) {
            $em = $doctrine->getManager();
            $candidate = $candidateRepository->findOneBy(['email_candidate' => $email]);
            if ($candidate instanceof Candidate) {
                $em->remove($candidate);
                $em->flush();
                return $this->json([
                    'success' => true,
                    'message' => "Candidat " . $email . " supprimé !"
                ]);
            }
            return $this->json([
                'success' => false,
                'message' => "Le candidat recherché n'existe pas ou a déjà été supprimé..."
            ]);
        }
        return $this->json([
            'success' => false,
            'message' => "Format de l'email incorrect..."
        ]);
    }
}
